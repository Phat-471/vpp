<?php

namespace Tests\Feature;

use App\Livewire\PosCustomers;
use App\Livewire\PosTerminal;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class PosCustomersTest extends TestCase
{
    use RefreshDatabase;

    public function test_customers_page_requires_staff_and_cashier_permission(): void
    {
        $this->get('/pos/khach-hang')->assertRedirect();
        $this->actingAs(User::factory()->create(['role' => 'technician']))->get('/pos/khach-hang')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'cashier']))->get('/pos/khach-hang')->assertOk()->assertSee('Thêm khách hàng');
    }

    public function test_cashier_can_create_normalized_contact_without_account_or_debt_mutation(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        Livewire::test(PosCustomers::class)->call('newCustomer')
            ->set('form', ['name' => 'Khách thử nghiệm', 'phone' => '+84 901 234 567', 'email' => 'test@example.com', 'address' => '12 Đường thử nghiệm', 'notes' => 'Liên hệ trước khi giao', 'debt_balance' => 10000, 'password' => 'injected'])
            ->call('saveCustomer')->assertHasNoErrors()->assertSet('formOpen', false);
        $customer = Customer::firstOrFail();
        $this->assertSame('0901234567', $customer->phone);
        $this->assertSame('4567', $customer->phone_last4);
        $this->assertSame('0.00', $customer->debt_balance);
        $this->assertNull($customer->password);
    }

    public function test_server_rejects_invalid_fields_and_formatted_duplicate_phone(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        Customer::create(['name' => 'Khách cũ', 'phone' => '0901.234.567']);
        Livewire::test(PosCustomers::class)->set('form', ['name' => '1', 'phone' => 'abc', 'email' => 'invalid', 'address' => 'a'])
            ->call('saveCustomer')->assertHasErrors(['name', 'phone', 'email', 'address']);
        Livewire::test(PosCustomers::class)->set('form', ['name' => ['invalid'], 'phone' => ['invalid']])
            ->call('saveCustomer')->assertHasErrors(['name', 'phone']);
        Livewire::test(PosCustomers::class)->set('form', ['name' => 'Khách mới', 'phone' => '+84 901 234 567'])
            ->call('saveCustomer')->assertHasErrors(['phone']);
        $this->assertSame(1, Customer::count());
    }

    public function test_edit_shares_admin_data_and_does_not_expose_credentials_from_injected_keys(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $customer = Customer::create(['name' => 'Khách cũ', 'phone' => '0901234567', 'password' => 'secret-test-only']);
        Livewire::test(PosCustomers::class)->set('form.password', 'injected')->call('editCustomer', $customer->id)
            ->assertSet('form', ['name' => 'Khách cũ', 'phone' => '0901234567', 'email' => '', 'address' => '', 'notes' => ''])
            ->set('form.name', 'Khách cập nhật')->call('saveCustomer')->assertHasNoErrors();
        $this->assertSame('Khách cập nhật', $customer->fresh()->name);
    }

    public function test_account_phone_cannot_be_changed_but_contact_notes_can(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $customer = Customer::create(['name' => 'Khách tài khoản', 'phone' => '0901234567', 'phone_verified_at' => now()]);
        Livewire::test(PosCustomers::class)->call('editCustomer', $customer->id)->set('form.phone', '0901234568')
            ->call('saveCustomer')->assertHasErrors('phone');
        $this->assertSame('0901234567', $customer->fresh()->phone);
    }

    public function test_cashier_cannot_delete_and_admin_can_delete_only_unused_contacts(): void
    {
        $customer = Customer::create(['name' => 'Khách chưa mua', 'phone' => '0901234567']);
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        Livewire::test(PosCustomers::class)->call('deleteCustomer', $customer->id)->assertForbidden();
        $this->assertModelExists($customer);
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        Livewire::test(PosCustomers::class)->call('deleteCustomer', $customer->id)->assertHasNoErrors();
        $this->assertModelMissing($customer);
        foreach ([['debt_balance' => 5000], ['password' => 'test-password'], ['zalo_id' => 'test-zalo-id'], ['phone_verified_at' => now()]] as $attributes) {
            $protected = Customer::create($attributes + ['name' => 'Khách được bảo vệ', 'phone' => '0901234568']);
            Livewire::test(PosCustomers::class)->call('deleteCustomer', $protected->id)->assertHasErrors('customer');
            $this->assertModelExists($protected);
        }
    }

    public function test_customers_with_orders_are_preserved_and_picker_keeps_cart(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $customer = Customer::create(['name' => 'Khách đã mua', 'phone' => '0901234567']);
        $order = $customer->orders()->create(['uuid' => (string) Str::uuid(), 'order_code' => 'TEST-CUSTOMER', 'channel' => 'pos', 'payment_method' => 'cash', 'payment_status' => 'paid', 'status' => 'completed', 'created_by' => auth()->id(), 'subtotal' => 1000, 'grand_total' => 1000]);
        Livewire::test(PosCustomers::class)->call('deleteCustomer', $customer->id)->assertHasErrors('customer');
        $this->assertSame($customer->id, $order->fresh()->customer_id);
        Livewire::test(PosCustomers::class, ['selectForSale' => true])->call('selectCustomer', $customer->id)
            ->assertDispatched('pos-customer-selected', id: $customer->id);
        $product = Product::create(['sku' => 'TEST-PICKER', 'slug' => 'test-picker', 'name' => 'Giấy kiểm thử', 'retail_price' => 10000, 'cost_price' => 8000, 'stock_quantity' => 5, 'base_unit' => 'Ram', 'is_active' => true]);
        $component = Livewire::test(PosTerminal::class)->call('addToCart', $product->id)->set('customerManagerOpen', true)->assertSee('Chọn và quản lý khách hàng');
        $cart = $component->get('cart');
        $component->dispatch('pos-customer-selected', id: $customer->id)
            ->assertSet('customerName', 'Khách đã mua')->assertSet('customerPhone', '0901234567')->assertSet('cart', $cart);
    }

    public function test_formatted_legacy_contact_is_used_for_sale_without_duplicate_customer(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $customer = Customer::create(['name' => 'Khách cũ', 'phone' => '+84 901 234 567']);
        $product = Product::create(['sku' => 'TEST-LEGACY', 'slug' => 'test-legacy', 'name' => 'Giấy kiểm thử', 'retail_price' => 10000, 'cost_price' => 8000, 'stock_quantity' => 5, 'base_unit' => 'Ram', 'is_active' => true]);
        Livewire::test(PosTerminal::class)->call('addToCart', $product->id)->dispatch('pos-customer-selected', id: $customer->id)
            ->set('cashGiven', '10000')->call('checkout')->assertHasNoErrors();
        $this->assertSame(1, Customer::count());
        $order = Order::firstOrFail();
        $this->assertSame($customer->id, $order->customer_id);
        $customer->update(['name' => 'Tên mới']);
        $this->assertSame('Khách cũ', $order->fresh()->customer_name);
    }

    public function test_customer_with_repair_ticket_cannot_be_deleted(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $customer = Customer::create(['name' => 'Khách sửa máy', 'phone' => '0901234567']);
        $customer->repairTickets()->create(['uuid' => (string) Str::uuid(), 'ticket_code' => 'TEST-REPAIR', 'customer_name' => $customer->name, 'customer_phone' => $customer->phone, 'phone_last4' => '4567', 'device_name' => 'Máy kiểm thử', 'issue_description' => 'Kiểm tra máy']);
        Livewire::test(PosCustomers::class)->call('deleteCustomer', $customer->id)->assertHasErrors('customer');
        $this->assertModelExists($customer);
    }

    public function test_search_and_pagination(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        for ($i = 0; $i < 12; $i++) {
            Customer::create(['name' => 'Khách kiểm thử '.$i, 'phone' => '09012345'.sprintf('%02d', $i)]);
        }
        Livewire::test(PosCustomers::class)->assertSee('Khách kiểm thử 11')->assertDontSee('Khách kiểm thử 0</strong>', false)
            ->call('nextPage', 'customersPage')->assertSee('Khách kiểm thử 0')
            ->set('search', '0901234511')->assertSee('Khách kiểm thử 11')->assertDontSee('Khách kiểm thử 0');
    }
}
