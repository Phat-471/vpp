<?php

namespace Tests\Feature;

use App\Actions\Pos\CreatePosOrder;
use App\Livewire\PosTerminal;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class PosTerminalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Existing checkout tests cover optional mode; PosShiftsTest covers mandatory mode.
        Setting::set('pos_shift_required', '0', 'pos');
    }

    private function product(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'sku' => 'SP-'.Str::random(8), 'slug' => Str::uuid()->toString(),
            'name' => 'Giấy A4 70gsm', 'retail_price' => 75000, 'cost_price' => 60000,
            'stock_quantity' => 20, 'base_unit' => 'Ram', 'is_active' => true,
        ], $overrides));
    }

    private function input(array $overrides = []): array
    {
        return array_merge([
            'customer_name' => '', 'customer_phone' => '', 'notes' => '',
            'discount' => 0, 'cash_given' => 1000000, 'payment_method' => 'cash',
        ], $overrides);
    }

    public function test_pos_has_its_own_login_and_requires_a_cashier_account(): void
    {
        $this->get('/pos')->assertRedirect(route('pos.login'));
        $this->get('/pos/login')->assertOk()->assertSee('Bắt đầu phiên bán hàng');
        $this->actingAs(User::factory()->create(['role' => 'technician']))->get('/pos')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'cashier']))->get('/pos')
            ->assertOk()->assertSee('Danh sách sản phẩm')->assertDontSee('fi-sidebar', false);
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/pos')->assertOk();
    }

    public function test_cashier_can_log_in_and_log_out_through_pos_urls(): void
    {
        $user = User::factory()->create(['role' => 'cashier']);
        $this->post('/pos/login', ['identifier' => $user->email, 'password' => 'password'])->assertRedirect(route('pos.index'));
        $this->assertAuthenticatedAs($user);
        $this->post('/pos/logout')->assertRedirect(route('pos.login'));
        $this->assertGuest();
    }

    public function test_technician_login_does_not_grant_pos_access(): void
    {
        $user = User::factory()->create(['role' => 'technician']);
        $this->post('/pos/login', ['identifier' => $user->email, 'password' => 'password'])->assertSessionHasErrors('identifier');
        $this->assertGuest();
    }

    public function test_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/pos/login', ['identifier' => 'cashier-test@example.test', 'password' => 'invalid']);
        }
        $this->post('/pos/login', ['identifier' => 'cashier-test@example.test', 'password' => 'invalid'])->assertStatus(429);
    }

    public function test_product_list_has_no_photos_and_filters_by_name_and_category(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $category = Category::create(['name' => 'Giấy in', 'slug' => 'giay-in', 'is_active' => true]);
        $paper = $this->product(['category_id' => $category->id]);
        $this->product(['name' => 'Bút bi xanh']);
        $component = Livewire::test(PosTerminal::class)->assertSee('Bút bi xanh')->assertDontSee('<img', false);
        $component->set('search', 'Giấy A4')->assertSee($paper->name)->assertDontSee('Bút bi xanh');
        $component->set('search', '')->set('selectedCategory', $category->id)->assertSee($paper->name)->assertDontSee('Bút bi xanh');
    }

    public function test_cash_checkout_deducts_stock_and_repeat_submission_does_not_duplicate_order(): void
    {
        $user = User::factory()->create(['role' => 'cashier']);
        $this->actingAs($user);
        $product = $this->product();
        $component = Livewire::test(PosTerminal::class)->call('addToCart', $product->id)
            ->set('cashGiven', '75000')->call('checkout')->assertHasNoErrors()->assertSee('Đã thanh toán');
        $order = Order::firstOrFail();
        $this->assertEquals(75000, $order->grand_total);
        $this->assertEquals(75000, $order->paid_amount);
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame(19, $product->refresh()->stock_quantity);
        $component->call('checkout')->assertHasNoErrors();
        app(CreatePosOrder::class)->handle($user, [['product_id' => $product->id, 'quantity' => 1]], $this->input(), $order->uuid);
        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(19, $product->refresh()->stock_quantity);
    }

    public function test_unit_conversion_uses_server_prices_and_deducts_base_stock(): void
    {
        $user = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        $unit = $product->units()->create(['unit_name' => 'Thùng', 'conversion_rate' => 5, 'price' => 360000]);
        $order = app(CreatePosOrder::class)->handle($user, [[
            'product_id' => $product->id, 'product_unit_id' => $unit->id, 'quantity' => 2,
            'unit_price' => 1, 'subtotal' => 2,
        ]], $this->input(), (string) Str::uuid());
        $this->assertEquals(720000, $order->grand_total);
        $this->assertSame(10, $product->refresh()->stock_quantity);
    }

    public function test_insufficient_cash_does_not_create_an_order_or_change_stock(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $product = $this->product();
        Livewire::test(PosTerminal::class)->call('addToCart', $product->id)->set('cashGiven', '1000')
            ->call('checkout')->assertHasErrors('cashGiven');
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(20, $product->refresh()->stock_quantity);
    }

    public function test_combined_units_cannot_oversell_the_same_product(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $product = $this->product(['stock_quantity' => 5]);
        $unit = $product->units()->create(['unit_name' => 'Thùng', 'conversion_rate' => 5, 'price' => 360000]);
        Livewire::test(PosTerminal::class)->call('addToCart', $product->id, $unit->id)
            ->call('addToCart', $product->id)->assertHasErrors('cart');
        $this->assertSame(5, $product->refresh()->stock_quantity);
    }

    public function test_a_foreign_unit_is_rejected_without_partial_writes(): void
    {
        $user = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        $other = $this->product();
        $unit = $other->units()->create(['unit_name' => 'Thùng', 'conversion_rate' => 5, 'price' => 360000]);
        try {
            app(CreatePosOrder::class)->handle($user, [['product_id' => $product->id, 'product_unit_id' => $unit->id, 'quantity' => 1]], $this->input(), (string) Str::uuid());
            $this->fail('Foreign unit was accepted.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('orders', 0);
            $this->assertSame(20, $product->refresh()->stock_quantity);
        }
    }

    public function test_vietqr_remains_unpaid_and_updates_only_after_server_confirmation(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $product = $this->product();
        $component = Livewire::test(PosTerminal::class)->call('addToCart', $product->id)
            ->set('paymentMethod', 'vietqr')->call('checkout')->assertHasNoErrors()->assertSee('Chờ thanh toán VietQR');
        $order = Order::firstOrFail();
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertEquals(0, $order->paid_amount);
        $component->call('refreshPayment');
        $this->assertSame('unpaid', $order->refresh()->payment_status);
        $order->update(['paid_amount' => $order->grand_total, 'payment_status' => 'paid', 'status' => 'completed']);
        $component->call('refreshPayment')->assertSee('Đã thanh toán');
    }

    public function test_draft_is_restored_without_creating_an_order(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $product = $this->product();
        Livewire::test(PosTerminal::class)->call('addToCart', $product->id)->call('saveDraft')
            ->assertSet('cart', [])->call('restoreDraft')->assertSee($product->name)->assertHasNoErrors();
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(20, $product->refresh()->stock_quantity);
    }

    public function test_cashier_cannot_read_another_cashiers_receipt(): void
    {
        $owner = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        $order = app(CreatePosOrder::class)->handle($owner, [['product_id' => $product->id, 'quantity' => 1]], $this->input(), (string) Str::uuid());
        $this->actingAs($owner)->get(route('pos.receipt', $order->uuid))->assertOk();
        $this->actingAs(User::factory()->create(['role' => 'cashier']))->get(route('pos.receipt', $order->uuid))->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('pos.receipt', $order->uuid))->assertOk();
    }

    public function test_receipt_uses_store_settings_and_persisted_cash_tender(): void
    {
        $user = User::factory()->create(['role' => 'cashier']);
        $this->actingAs($user);
        foreach (['site_name' => 'Cửa hàng kiểm thử', 'address' => 'Địa chỉ kiểm thử', 'hotline' => '1900 0000'] as $key => $value) {
            Setting::set($key, $value);
        }
        $product = $this->product();
        $order = app(CreatePosOrder::class)->handle($user, [['product_id' => $product->id, 'quantity' => 1]], $this->input(['cash_given' => 100000]), (string) Str::uuid());
        $this->assertSame(100000, $order->cash_received);
        $this->assertEquals(75000, $order->paid_amount);
        $this->get(route('pos.receipt', $order->uuid).'?autoprint=1')->assertOk()
            ->assertSee('Cửa hàng kiểm thử')->assertSee('Địa chỉ kiểm thử')->assertSee('1900 0000')
            ->assertSee('Tiền khách đưa:')->assertSee('100.000')->assertSee('Tiền thừa trả khách:')->assertSee('25.000')
            ->assertSee('pos-receipt.js');
        $order->update(['cash_received' => null]);
        $this->get(route('pos.receipt', $order->uuid))->assertSee('Đơn cũ chưa lưu thông tin');
    }

    public function test_cash_sale_requires_print_confirmation_before_next_sale(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $product = $this->product();
        $component = Livewire::test(PosTerminal::class)->call('addToCart', $product->id)
            ->set('cashGiven', '100000')->call('checkout')->assertDispatched('pos-print-receipt');
        $order = Order::firstOrFail();
        $component->call('newSale')->assertHasErrors('receipt')->assertSet('completedOrderUuid', $order->uuid);
        $component->call('reprintReceipt')->assertDispatched('pos-print-receipt');
        $component->call('confirmReceiptPrinted')->assertHasNoErrors();
        $this->assertNotNull($order->refresh()->receipt_confirmed_at);
        $component->call('newSale')->assertSet('completedOrderUuid', null)->assertSet('cart', []);
        $this->assertSame(19, $product->refresh()->stock_quantity);
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_qr_receipt_prints_only_after_server_payment_confirmation(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $product = $this->product();
        $component = Livewire::test(PosTerminal::class)->call('addToCart', $product->id)
            ->set('paymentMethod', 'vietqr')->call('checkout')->assertNotDispatched('pos-print-receipt');
        $order = Order::firstOrFail();
        $this->assertNull($order->cash_received);
        $component->call('confirmReceiptPrinted')->assertHasErrors('receipt');
        $this->assertNull($order->refresh()->receipt_confirmed_at);
        $order->update(['payment_status' => 'paid', 'paid_amount' => $order->grand_total]);
        $component->call('refreshPayment')->assertDispatched('pos-print-receipt');
        $component->call('confirmReceiptPrinted')->assertHasNoErrors();
        $this->get(route('pos.receipt', $order->uuid))->assertDontSee('Tiền khách đưa:');
    }
}
