<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class LookupSystemTest extends TestCase
{
    use RefreshDatabase;

    private function createSampleOrder(): Order
    {
        $user = User::factory()->create(['role' => 'admin']);

        $customer = Customer::create([
            'name' => 'Trần Văn Nam',
            'phone' => '0987654321',
            'phone_last4' => '4321',
            'address' => '12 Nguyễn Huệ, Quận 1',
        ]);

        $product = Product::create([
            'sku' => 'SP-TEST-002',
            'name' => 'Bút bi Thiên Long 0.5mm',
            'slug' => 'but-bi-thien-long-05mm',
            'base_unit' => 'Cây',
            'retail_price' => 5000,
            'cost_price' => 3000,
            'stock_quantity' => 100,
            'is_active' => true,
        ]);

        $order = Order::create([
            'uuid' => (string) Str::uuid(),
            'order_code' => 'HD260088',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'customer_address' => $customer->address,
            'channel' => 'online',
            'status' => 'pending',
            'subtotal' => 25000,
            'discount_amount' => 0,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'grand_total' => 25000,
            'paid_amount' => 0,
            'payment_status' => 'unpaid',
            'payment_method' => 'vietqr',
            'created_by' => $user->id,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_name' => 'Cây',
            'quantity' => 5,
            'unit_price' => 5000,
            'subtotal' => 25000,
        ]);

        return $order;
    }

    public function test_can_view_lookup_page(): void
    {
        $response = $this->get(route('lookup.index'));

        $response->assertOk();
        $response->assertSee('Tra cứu tiến độ');
        $response->assertSee('HDxxxx');
        $response->assertSee('SCxxxx');
    }

    public function test_lookup_order_with_valid_phone_last4_redirects_and_displays_order(): void
    {
        $order = $this->createSampleOrder();

        $response = $this->post(route('lookup.search'), [
            'code' => $order->order_code,
            'phone_last4' => '4321',
        ]);

        $response->assertRedirect(route('lookup.order', ['code' => $order->order_code]));

        $follow = $this->get(route('lookup.order', ['code' => $order->order_code]));
        $follow->assertOk();
        $follow->assertSee('HD260088');
        $follow->assertSee('Bút bi Thiên Long 0.5mm');
        $follow->assertSee('TIẾN ĐỘ THỰC HIỆN');
        $follow->assertSee('Chưa thanh toán');
    }

    public function test_lookup_order_with_invalid_phone_last4_is_blocked_for_security(): void
    {
        $order = $this->createSampleOrder();

        $response = $this->from(route('lookup.index'))->post(route('lookup.search'), [
            'code' => $order->order_code,
            'phone_last4' => '9999',
        ]);

        $response->assertRedirect(route('lookup.index'));
        $response->assertSessionHasErrors(['lookup_error']);
    }

    public function test_direct_order_lookup_without_verification_renders_verify_form(): void
    {
        $order = $this->createSampleOrder();

        $response = $this->get(route('lookup.order', ['code' => $order->order_code]));

        $response->assertOk();
        $response->assertSee('Xác Thực Bảo Mật Đơn Hàng');
        $response->assertSee('HD260088');
    }

    public function test_direct_order_lookup_with_phone4_param_succeeds(): void
    {
        $order = $this->createSampleOrder();

        $response = $this->get(route('lookup.order', [
            'code' => $order->order_code,
            'phone4' => '4321',
        ]));

        $response->assertOk();
        $response->assertSee('HD260088');
        $response->assertSee('Trần Văn Nam');
    }
}
