<?php

namespace Tests\Feature;

use App\Livewire\PosOrders;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class PosOrdersTest extends TestCase
{
    use RefreshDatabase;

    private function order(User $user, array $attributes = []): Order
    {
        return Order::create(array_merge([
            'uuid' => (string) Str::uuid(), 'order_code' => 'TEST-'.Str::random(8),
            'created_by' => $user->id, 'channel' => 'pos', 'status' => 'completed',
            'customer_name' => 'Khách kiểm thử', 'customer_phone' => '0901234567',
            'subtotal' => 10000, 'grand_total' => 10000, 'paid_amount' => 10000,
            'payment_method' => 'cash', 'payment_status' => 'paid', 'cash_received' => 20000,
        ], $attributes));
    }

    public function test_page_requires_staff_cashier_access(): void
    {
        $this->get('/pos/don-hang')->assertRedirect();
        $this->actingAs(User::factory()->create(['role' => 'technician']))->get('/pos/don-hang')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'cashier']))->get('/pos/don-hang')->assertOk()->assertSee('Đơn hàng POS');
    }

    public function test_cashier_list_cannot_leak_another_cashier_or_online_order(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $own = $this->order($cashier);
        $other = $this->order(User::factory()->create(['role' => 'cashier']));
        $online = $this->order($cashier, ['channel' => 'online']);
        $this->actingAs($cashier);
        Livewire::test(PosOrders::class)->assertSee($own->order_code)->assertDontSee($other->order_code)->assertDontSee($online->order_code)
            ->set('search', $other->order_code)->call('applyFilters')->assertDontSee($other->order_code)->assertSee('Chưa có đơn hàng phù hợp');
        Livewire::test(PosOrders::class)->call('showOrder', $other->uuid)->assertForbidden();
        Livewire::test(PosOrders::class)->call('showOrder', $online->uuid)->assertForbidden();
    }

    public function test_admin_sees_all_pos_but_cannot_open_online_order(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $pos = $this->order($cashier);
        $online = $this->order($cashier, ['channel' => 'online']);
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        Livewire::test(PosOrders::class)->assertSee($pos->order_code)->assertDontSee($online->order_code)
            ->call('showOrder', $pos->uuid)->assertSet('selectedUuid', $pos->uuid)->assertSee('CHI TIẾT ĐƠN HÀNG');
        Livewire::test(PosOrders::class)->call('showOrder', $online->uuid)->assertForbidden();
    }

    public function test_filters_match_code_phone_dates_and_payment_and_reject_invalid_values(): void
    {
        $user = User::factory()->create(['role' => 'cashier']);
        $matching = $this->order($user, ['created_at' => '2026-10-08 23:59:59', 'payment_status' => 'unpaid', 'paid_amount' => 0, 'payment_method' => 'vietqr']);
        $older = $this->order($user, ['created_at' => '2026-10-07 12:00:00']);
        $this->actingAs($user);
        Livewire::test(PosOrders::class)->set('search', '+84 901 234 567')->set('from', '2026-10-08')->set('to', '2026-10-08')->set('payment', 'unpaid')
            ->call('applyFilters')->assertHasNoErrors()->assertSee($matching->order_code)->assertDontSee($older->order_code)
            ->call('clearFilters')->set('search', $older->order_code)->call('applyFilters')->assertSee($older->order_code)->assertDontSee($matching->order_code);
        Livewire::test(PosOrders::class)->set('from', '2026-10-08')->set('to', '2026-10-07')->call('applyFilters')->assertHasErrors('to');
        Livewire::test(PosOrders::class)->set('from', '2026-02-31')->call('applyFilters')->assertHasErrors('from');
        Livewire::test(PosOrders::class)->set('payment', 'fake')->set('search', str_repeat('a', 101))->call('applyFilters')->assertHasErrors(['payment', 'search']);
    }

    public function test_detail_uses_saved_lines_and_cash_and_print_does_not_mutate_order(): void
    {
        $user = User::factory()->create(['role' => 'cashier']);
        $order = $this->order($user);
        $product = Product::create(['sku' => 'TEST-HISTORY', 'slug' => 'test-history', 'name' => 'Tên sản phẩm hiện tại', 'retail_price' => 15000, 'cost_price' => 8000, 'stock_quantity' => 5, 'base_unit' => 'Ram', 'is_active' => true]);
        $order->orderItems()->create(['product_id' => $product->id, 'product_name' => 'Hàng đã mua', 'unit_name' => 'Ram', 'quantity' => 1, 'unit_price' => 10000, 'cost_price' => 8000, 'subtotal' => 10000]);
        $this->actingAs($user);
        $before = $order->fresh()->getAttributes();
        $stockBefore = $product->fresh()->stock_quantity;
        Setting::set('site_name', 'Cửa hàng kiểm thử bill');
        Livewire::test(PosOrders::class)->call('showOrder', $order->uuid)->assertSee('Hàng đã mua')
            ->assertSee('Tiền khách đưa')->assertSee('20.000 ₫')->assertSee('Tiền thừa trả khách')->assertSee('In lại bill')
            ->call('closeOrder')->assertSet('selectedUuid', null);
        $this->get(route('pos.receipt', $order->uuid).'?autoprint=1')->assertOk()->assertSee('Cửa hàng kiểm thử bill')->assertSee('pos-receipt.js')->assertSee('Tiền thừa trả khách');
        $this->assertSame($before, $order->fresh()->getAttributes());
        $this->assertSame(1, Order::count());
        $this->assertSame(1, $order->orderItems()->count());
        $this->assertSame($stockBefore, $product->fresh()->stock_quantity);
    }

    public function test_legacy_cash_is_not_fabricated_and_partial_payment_has_remaining_amount(): void
    {
        $user = User::factory()->create(['role' => 'cashier']);
        $order = $this->order($user, ['cash_received' => null, 'payment_status' => 'partially_paid', 'paid_amount' => 3000]);
        $this->actingAs($user);
        Livewire::test(PosOrders::class)->call('showOrder', $order->uuid)->assertSee('Thanh toán một phần')
            ->assertSee('7.000 ₫')->assertSee('Đơn cũ chưa lưu tiền khách đưa')->assertDontSee('Tiền thừa trả khách');
    }

    public function test_list_is_paginated_and_filter_resets_page(): void
    {
        $user = User::factory()->create(['role' => 'cashier']);
        $first = $this->order($user, ['created_at' => '2026-10-01 00:00:00']);
        for ($i = 0; $i < 15; $i++) {
            $this->order($user, ['created_at' => '2026-10-02 00:00:00']);
        }
        $this->actingAs($user);
        Livewire::test(PosOrders::class)->assertDontSee($first->order_code)->call('nextPage')->assertSee($first->order_code)
            ->set('search', $first->order_code)->call('applyFilters')->assertSet('paginators.page', 1)->assertSee($first->order_code);
    }

    public function test_customer_notes_and_names_are_escaped_and_invalid_uuid_is_rejected(): void
    {
        $user = User::factory()->create(['role' => 'cashier']);
        $order = $this->order($user, ['customer_name' => '<script>test-name</script>', 'notes' => '<script>test-note</script>']);
        $this->actingAs($user);
        Livewire::test(PosOrders::class)->call('showOrder', $order->uuid)->assertSee('&lt;script&gt;test-note&lt;/script&gt;', false)
            ->assertDontSee('<script>test-note</script>', false)->assertDontSee('<script>test-name</script>', false);
        Livewire::test(PosOrders::class)->call('showOrder', 'invalid-id')->assertHasErrors('order');
    }
}
