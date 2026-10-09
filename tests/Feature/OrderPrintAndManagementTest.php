<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderPrintAndManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set('site_name', 'VPP Ánh Dương');
        Setting::set('hotline', '0912.345.678');
        Setting::set('address', '123 Phố Văn Phòng Phẩm, Quận 1');
    }

    private function createSampleOrder(array $attributes = []): Order
    {
        $user = User::factory()->create(['role' => 'admin']);

        $customer = Customer::create([
            'name' => 'Công Ty Thiết Kế An Phát',
            'phone' => '0933221100',
            'phone_last4' => '1100',
            'address' => '456 Lê Lợi, TP.HCM',
        ]);

        $product = Product::create([
            'sku' => 'SP-TEST-001',
            'name' => 'Giấy in A4 Double A 70gsm',
            'slug' => 'giay-in-a4-double-a-70gsm',
            'base_unit' => 'Ram',
            'retail_price' => 75000,
            'cost_price' => 60000,
            'stock_quantity' => 100,
            'is_active' => true,
        ]);

        $order = Order::create(array_merge([
            'uuid' => (string) Str::uuid(),
            'order_code' => 'HD260099',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'customer_address' => $customer->address,
            'channel' => 'pos',
            'status' => 'completed',
            'subtotal' => 150000,
            'discount_amount' => 10000,
            'tax_rate' => 10,
            'tax_amount' => 14000,
            'grand_total' => 154000,
            'paid_amount' => 154000,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'cash_received' => 200000,
            'is_vat_invoice' => true,
            'company_name' => 'CÔNG TY TNHH AN PHÁT TECH',
            'company_tax_id' => '0312345678',
            'company_address' => '456 Lê Lợi, Phường Bến Nghé, Quận 1',
            'invoice_email' => 'ketoan@anphat.vn',
            'created_by' => $user->id,
        ], $attributes));

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_name' => $product->base_unit,
            'quantity' => 2,
            'conversion_rate' => 1,
            'unit_price' => 75000,
            'cost_price' => 60000,
            'subtotal' => 150000,
        ]);

        return $order;
    }

    public function test_can_view_a5_order_receipt(): void
    {
        $order = $this->createSampleOrder();

        $response = $this->get(route('print.order', $order->id));

        $response->assertOk();
        $response->assertSee('HD260099');
        $response->assertSee('VPP Ánh Dương');
        $response->assertSee('Công Ty Thiết Kế An Phát');
        $response->assertSee('Giấy in A4 Double A 70gsm');
        $response->assertSee('Khổ A5 (Mặc định)');
        $response->assertSee('Khổ K80 (Nhiệt 80mm)');
        $response->assertSee('154.000 ₫');
    }

    public function test_can_view_k80_order_receipt(): void
    {
        $order = $this->createSampleOrder();

        $response = $this->get(route('print.order', ['id' => $order->id, 'format' => 'k80']));

        $response->assertOk();
        $response->assertSee('HD260099');
        $response->assertSee('Máy in nhiệt K80 (80mm)');
        $response->assertSee('size: 80mm auto', false);
    }

    public function test_can_view_vat_invoice_print(): void
    {
        $order = $this->createSampleOrder();

        $response = $this->get(route('print.vat-invoice', $order->id));

        $response->assertOk();
        $response->assertSee('HD260099');
        $response->assertSee('CÔNG TY TNHH AN PHÁT TECH');
        $response->assertSee('0312345678');
        $response->assertSee('Giấy in A4 Double A 70gsm');
    }

    public function test_print_returns_404_when_order_not_found(): void
    {
        $this->get('/print/order/999999')->assertNotFound();
        $this->get('/print/vat-invoice/999999')->assertNotFound();
    }
}
