<?php

namespace Tests\Feature;

use App\Actions\Pos\CreatePosOrder;
use App\Livewire\PosOrders;
use App\Livewire\PosShifts;
use App\Livewire\PosTerminal;
use App\Models\Order;
use App\Models\PosShift;
use App\Models\Product;
use App\Models\User;
use App\Services\PosShiftService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class PosShiftsTest extends TestCase
{
    use RefreshDatabase;

    private function staff(string $role = 'cashier'): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);

        return $user;
    }

    private function sale(User $user, string $method = 'cash', ?string $uuid = null): Order
    {
        $product = Product::create(['sku' => Str::random(12), 'slug' => (string) Str::uuid(), 'name' => 'Giấy kiểm thử', 'base_unit' => 'Ram', 'retail_price' => 10000, 'cost_price' => 8000, 'stock_quantity' => 10, 'is_active' => true]);

        return app(CreatePosOrder::class)->handle($user, [['product_id' => $product->id, 'product_unit_id' => null, 'quantity' => 1]], [
            'customer_name' => '', 'customer_phone' => '', 'notes' => '', 'discount' => 0, 'cash_given' => 20000, 'payment_method' => $method,
        ], $uuid ?? (string) Str::uuid());
    }

    public function test_default_requires_shift_and_rejects_checkout_without_database_mutation(): void
    {
        $user = $this->staff();
        $product = Product::create(['sku' => 'SHIFT-GUARD', 'slug' => 'shift-guard', 'name' => 'Giấy kiểm thử', 'base_unit' => 'Ram', 'retail_price' => 10000, 'cost_price' => 8000, 'stock_quantity' => 10, 'is_active' => true]);
        $this->assertTrue(app(PosShiftService::class)->required());
        Livewire::test(PosTerminal::class)->call('addToCart', $product->id)->set('cashGiven', '20000')->call('checkout')->assertHasErrors('shift');
        $this->assertSame(0, Order::count());
        $this->assertSame(10, $product->fresh()->stock_quantity);
    }

    public function test_open_shift_validates_cash_and_prevents_duplicate_active_shift(): void
    {
        $this->staff();
        foreach (['-1', '1.5', 'abc', '1000000000001'] as $value) {
            Livewire::test(PosShifts::class)->set('openingCash', $value)->call('openShift')->assertHasErrors('openingCash');
        }
        Livewire::test(PosShifts::class)->set('openingCash', '100000')->call('openShift')->assertHasNoErrors();
        Livewire::test(PosShifts::class)->set('openingCash', '0')->call('openShift')->assertHasErrors('shift');
        $this->assertSame(1, PosShift::count());
    }

    public function test_close_snapshots_cash_net_of_change_and_reports_late_transfer_separately(): void
    {
        $user = $this->staff();
        $service = app(PosShiftService::class);
        $shift = $service->open($user, 100000);
        $cash = $this->sale($user);
        $bank = $this->sale($user, 'vietqr');
        $this->assertSame($shift->id, $cash->pos_shift_id);
        $this->assertFalse($cash->pos_outside_shift);
        $closed = $service->close($user, $shift->uuid, 110000, '');
        $this->assertSame('10000.00', $closed->cash_sales);
        $this->assertSame('0.00', $closed->transfer_sales);
        $this->assertSame('110000.00', $closed->expected_cash);
        $this->assertSame('0.00', $closed->difference);
        $this->assertSame(1, $closed->pending_transfer_count);
        $before = $closed->getAttributes();
        $bank->update(['paid_amount' => 10000, 'payment_status' => 'paid']);
        $this->assertSame('10000.00', $service->summary($closed)['late_transfer']);
        $this->assertSame($before, $closed->fresh()->getAttributes());
        $service->close($user, $shift->uuid, 0, 'Gửi lại yêu cầu');
        $this->assertSame('110000.00', $shift->fresh()->counted_cash);
        $service->open($user, 0);
        $this->assertSame($shift->id, $bank->fresh()->pos_shift_id);
    }

    public function test_difference_requires_reason_and_stale_tab_cannot_sell_after_close(): void
    {
        $user = $this->staff();
        $service = app(PosShiftService::class);
        $shift = $service->open($user, 100000);
        $screen = Livewire::test(PosShifts::class)->set('countedCash', '90000')->call('closeShift')->assertHasErrors('notes');
        $this->assertNull($shift->fresh()->closed_at);
        $screen->set('notes', 'Thiếu tiền kiểm thử')->call('closeShift')->assertHasNoErrors();
        $this->assertSame('-10000.00', $shift->fresh()->difference);
        $product = Product::create(['sku' => 'STALE-SHIFT', 'slug' => 'stale-shift', 'name' => 'Giấy kiểm thử', 'base_unit' => 'Ram', 'retail_price' => 10000, 'cost_price' => 8000, 'stock_quantity' => 10, 'is_active' => true]);
        Livewire::test(PosTerminal::class)->call('addToCart', $product->id)->set('cashGiven', '20000')->call('checkout')->assertHasErrors('shift');
        $this->assertSame(0, Order::count());
    }

    public function test_cashier_cannot_view_or_close_another_shift_or_change_mode(): void
    {
        $other = $this->staff();
        $shift = app(PosShiftService::class)->open($other, 100000);
        $this->staff();
        Livewire::test(PosShifts::class)->call('showShift', $shift->uuid)->assertForbidden();
        Livewire::test(PosShifts::class)->set('requiredEnabled', false)->call('saveMode')->assertForbidden();
        $this->assertTrue(app(PosShiftService::class)->required());
        $this->expectException(AuthorizationException::class);
        app(PosShiftService::class)->close(auth()->user(), $shift->uuid, 0, 'Không được phép');
    }

    public function test_admin_can_view_all_shifts_but_not_close_another_cashiers_shift(): void
    {
        $other = $this->staff();
        $shift = app(PosShiftService::class)->open($other, 100000);
        $this->staff('admin');
        Livewire::test(PosShifts::class)->call('showShift', $shift->uuid)->assertSee($other->name)->assertSee('100.000 ₫')
            ->set('countedCash', '100000')->call('closeShift')->assertForbidden();
    }

    public function test_optional_mode_marks_outside_orders_and_never_backfills_into_new_shift(): void
    {
        $admin = $this->staff('admin');
        Livewire::test(PosShifts::class)->set('requiredEnabled', false)->call('saveMode')->assertHasNoErrors();
        $outside = $this->sale($admin);
        $this->assertNull($outside->pos_shift_id);
        $this->assertTrue($outside->pos_outside_shift);
        $shift = app(PosShiftService::class)->open($admin, 0);
        $inside = $this->sale($admin);
        $this->assertSame($shift->id, $inside->pos_shift_id);
        $this->assertNull($outside->fresh()->pos_shift_id);
        Livewire::test(PosOrders::class)->set('shift', 'outside')->call('applyFilters')->assertSee($outside->order_code)->assertDontSee($inside->order_code);
        app(PosShiftService::class)->configure($admin, true);
        $this->assertTrue(app(PosShiftService::class)->required());
    }

    public function test_partial_transfer_is_snapshotted_and_repeated_checkout_keeps_original_shift(): void
    {
        $user = $this->staff();
        $service = app(PosShiftService::class);
        $shift = $service->open($user, 0);
        $uuid = (string) Str::uuid();
        $bank = $this->sale($user, 'vietqr', $uuid);
        $bank->update(['paid_amount' => '3000.25', 'payment_status' => 'partially_paid']);
        $closed = $service->close($user, $shift->uuid, 0, '');
        $this->assertSame('3000.25', $closed->transfer_sales);
        $bank->update(['paid_amount' => '10000.00', 'payment_status' => 'paid']);
        $this->assertSame('6999.75', $service->summary($closed)['late_transfer']);
        $retry = $this->sale($user, 'vietqr', $uuid);
        $this->assertSame($bank->id, $retry->id);
        $this->assertSame($shift->id, $retry->pos_shift_id);
        $this->assertSame(1, Order::count());
    }

    public function test_shifts_page_protects_staff_access_and_compact_open_keeps_cart(): void
    {
        $this->get('/pos/ca-ban-hang')->assertRedirect();
        $this->staff('technician');
        $this->get('/pos/ca-ban-hang')->assertForbidden();
        $this->staff();
        $this->get('/pos/ca-ban-hang')->assertOk()->assertSee('Ca thu ngân');
        $product = Product::create(['sku' => 'SHIFT-CART', 'slug' => 'shift-cart', 'name' => 'Giấy kiểm thử', 'base_unit' => 'Ram', 'retail_price' => 10000, 'cost_price' => 8000, 'stock_quantity' => 10, 'is_active' => true]);
        $terminal = Livewire::test(PosTerminal::class)->call('addToCart', $product->id);
        $cart = $terminal->get('cart');
        Livewire::test(PosShifts::class, ['compact' => true])->set('openingCash', '0')->call('openShift')->assertDispatched('pos-shift-changed');
        $terminal->dispatch('pos-shift-changed')->assertSet('cart', $cart)->assertHasNoErrors();
        $this->assertNotNull(app(PosShiftService::class)->current(auth()->user()));
        Livewire::test(PosShifts::class, ['compact' => true])->assertSee('Ca của mình đang mở');
    }
}
