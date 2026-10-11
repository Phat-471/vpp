<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderFulfillmentTest extends TestCase
{
    use RefreshDatabase;

    private function createCustomer(): Customer
    {
        return Customer::create([
            'name' => 'Nguyễn Văn Minh',
            'phone' => '0912345678',
            'phone_last4' => '5678',
            'address' => '123 Nguyễn Huệ, Quận 1',
        ]);
    }

    private function createProduct(array $attributes = []): Product
    {
        return Product::create(array_merge([
            'sku' => 'TEST-SKU-' . Str::random(5),
            'name' => 'Giấy in Bãi Bằng A4 70gsm',
            'slug' => 'giay-in-bai-bang-a4-70gsm-' . Str::random(5),
            'base_unit' => 'Ram',
            'retail_price' => 65000,
            'cost_price' => 50000,
            'stock_quantity' => 100,
            'is_active' => true,
        ], $attributes));
    }

    public function test_creating_order_item_deducts_stock(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct(['stock_quantity' => 50]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'channel' => 'web',
            'status' => 'pending',
            'subtotal' => 130000,
            'grand_total' => 130000,
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_name' => $product->base_unit,
            'quantity' => 2,
            'conversion_rate' => 1,
            'unit_price' => 65000,
            'subtotal' => 130000,
        ]);

        $this->assertSame(48, $product->fresh()->stock_quantity);
    }

    public function test_unit_conversion_deducts_correct_base_stock(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct(['stock_quantity' => 100]);

        $unit = ProductUnit::create([
            'product_id' => $product->id,
            'unit_name' => 'Thùng 5 Ram',
            'conversion_rate' => 5,
            'price' => 320000,
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'channel' => 'pos',
            'status' => 'processing',
            'subtotal' => 640000,
            'grand_total' => 640000,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
        ]);

        // Mua 2 thùng (mỗi thùng 5 Ram) = trừ 10 Ram
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_unit_id' => $unit->id,
            'product_name' => $product->name,
            'unit_name' => 'Thùng 5 Ram',
            'quantity' => 2,
            'conversion_rate' => 5,
            'unit_price' => 320000,
            'subtotal' => 640000,
        ]);

        $this->assertSame(90, $product->fresh()->stock_quantity);
    }

    public function test_cancelling_order_refunds_stock_automatically(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct(['stock_quantity' => 50]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'channel' => 'web',
            'status' => 'pending',
            'subtotal' => 195000,
            'grand_total' => 195000,
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_name' => $product->base_unit,
            'quantity' => 3,
            'conversion_rate' => 1,
            'unit_price' => 65000,
            'subtotal' => 195000,
        ]);

        $this->assertSame(47, $product->fresh()->stock_quantity);

        // Hủy đơn hàng
        $service = app(InventoryService::class);
        $service->updateOrderStatus($order, 'cancelled', ['notes' => 'Khách đổi ý hủy đơn']);

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame(50, $product->fresh()->stock_quantity);
    }

    public function test_reactivating_cancelled_order_rededucts_stock(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct(['stock_quantity' => 50]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'channel' => 'web',
            'status' => 'pending',
            'subtotal' => 130000,
            'grand_total' => 130000,
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_name' => $product->base_unit,
            'quantity' => 2,
            'conversion_rate' => 1,
            'unit_price' => 65000,
            'subtotal' => 130000,
        ]);

        $this->assertSame(48, $product->fresh()->stock_quantity);

        // Hủy đơn -> hoàn kho về 50
        $order->update(['status' => 'cancelled']);
        $this->assertSame(50, $product->fresh()->stock_quantity);

        // Khách gọi điện báo giao lại -> chuyển sang processing
        $order->update(['status' => 'processing']);
        $this->assertSame(48, $product->fresh()->stock_quantity);
    }

    public function test_updating_cancelled_order_does_not_double_refund(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct(['stock_quantity' => 50]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'channel' => 'web',
            'status' => 'pending',
            'subtotal' => 65000,
            'grand_total' => 65000,
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_name' => $product->base_unit,
            'quantity' => 1,
            'conversion_rate' => 1,
            'unit_price' => 65000,
            'subtotal' => 65000,
        ]);

        $this->assertSame(49, $product->fresh()->stock_quantity);

        // Hủy đơn lần 1
        $order->update(['status' => 'cancelled']);
        $this->assertSame(50, $product->fresh()->stock_quantity);

        // Lưu lại ghi chú mà trạng thái vẫn là cancelled
        $order->update(['notes' => 'Lý do: Không liên lạc được']);
        $this->assertSame(50, $product->fresh()->stock_quantity);
    }

    public function test_deleting_item_on_cancelled_order_does_not_double_refund(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct(['stock_quantity' => 50]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'channel' => 'web',
            'status' => 'pending',
            'subtotal' => 65000,
            'grand_total' => 65000,
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_name' => $product->base_unit,
            'quantity' => 2,
            'conversion_rate' => 1,
            'unit_price' => 65000,
            'subtotal' => 130000,
        ]);

        $this->assertSame(48, $product->fresh()->stock_quantity);

        // Hủy đơn -> hoàn kho về 50
        $order->update(['status' => 'cancelled']);
        $this->assertSame(50, $product->fresh()->stock_quantity);

        // Xóa item trong đơn đã hủy
        $item->delete();
        $this->assertSame(50, $product->fresh()->stock_quantity);
    }

    public function test_check_stock_availability(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct(['stock_quantity' => 5]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'channel' => 'web',
            'status' => 'pending',
            'subtotal' => 65000,
            'grand_total' => 65000,
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_name' => $product->base_unit,
            'quantity' => 1,
            'conversion_rate' => 1,
            'unit_price' => 65000,
            'subtotal' => 65000,
        ]);

        $service = app(InventoryService::class);
        $check = $service->checkOrderStockAvailability($order);
        $this->assertTrue($check['is_available']);

        // Nếu tăng quantity lên 10 vượt quá tồn kho (hiện còn 4)
        $item->quantity = 10;
        $item->save();

        $check2 = $service->checkOrderStockAvailability($order);
        $this->assertFalse($check2['is_available']);
        $this->assertCount(1, $check2['shortages']);
    }

    public function test_can_view_a4_dual_order_receipt(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct(['stock_quantity' => 50]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'channel' => 'pos',
            'status' => 'completed',
            'subtotal' => 65000,
            'grand_total' => 65000,
            'paid_amount' => 65000,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_name' => $product->base_unit,
            'quantity' => 1,
            'conversion_rate' => 1,
            'unit_price' => 65000,
            'subtotal' => 65000,
        ]);

        $response = $this->get(route('print.order', ['id' => $order->id, 'format' => 'a4_dual']));

        $response->assertOk();
        $response->assertSee('A4 - 2 Liên');
        $response->assertSee('ĐƯỜNG CẮT RỜI');
        $response->assertSee($order->order_code);
    }
}
