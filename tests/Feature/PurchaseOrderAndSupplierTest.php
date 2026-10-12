<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderAndSupplierTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_auto_code_generation(): void
    {
        $supplier1 = Supplier::create([
            'code' => Supplier::generateNextCode(),
            'name' => 'Công ty Thiên Long',
        ]);
        $this->assertEquals('NCC001', $supplier1->code);

        $supplier2 = Supplier::create([
            'code' => Supplier::generateNextCode(),
            'name' => 'Văn phòng phẩm Bến Nghé',
        ]);
        $this->assertEquals('NCC002', $supplier2->code);
    }

    public function test_purchase_order_intake_lifecycle_and_inventory_sync(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Bút bi', 'slug' => 'but-bi']);

        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'BUT-TL01',
            'name' => 'Bút bi Thiên Long 027',
            'slug' => 'but-bi-thien-long-027',
            'cost_price' => 3000,
            'retail_price' => 5000,
            'stock_quantity' => 10,
            'base_unit' => 'Cây',
        ]);

        $supplier = Supplier::create([
            'code' => 'NCC001',
            'name' => 'Tổng kho Thiên Long Miền Nam',
            'debt_balance' => 0,
        ]);

        $order = PurchaseOrder::create([
            'code' => PurchaseOrder::generateNextCode(),
            'supplier_id' => $supplier->id,
            'status' => 'draft',
            'total_amount' => 350000,
            'discount_amount' => 50000,
            'grand_total' => 300000,
            'paid_amount' => 100000,
            'debt_amount' => 200000,
            'payment_status' => 'partially_paid',
            'created_by' => $admin->id,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_name' => 'Cây',
            'quantity' => 100,
            'cost_price' => 3500, // Giá nhập mới cao hơn 3000
            'subtotal' => 350000,
        ]);

        // Trạng thái draft: tồn kho và nợ chưa thay đổi
        $this->assertEquals(10, $product->fresh()->stock_quantity);
        $this->assertEquals(0, $supplier->fresh()->debt_balance);

        // Kích hoạt nhận hàng nhập kho
        $service = app(InventoryService::class);
        $service->receivePurchaseOrder($order);

        // Kiểm tra sau khi nhận hàng:
        // 1. Tồn kho tăng từ 10 lên 110
        $this->assertEquals(110, $product->fresh()->stock_quantity);
        // 2. Giá vốn cập nhật lên 3500
        $this->assertEquals(3500, (float) $product->fresh()->cost_price);
        // 3. Công nợ NCC tăng đúng số tiền còn nợ (200.000đ)
        $this->assertEquals(200000, (float) $supplier->fresh()->debt_balance);
        // 4. Trạng thái phiếu là received
        $this->assertEquals('received', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->received_at);

        // Thử hủy phiếu nhập kho: hệ thống phải tự động hoàn tác tồn kho và nợ NCC
        $service->cancelPurchaseOrder($order, 'Hàng bị lỗi bao bì từ NCC');

        // Kiểm tra sau khi hủy:
        // 1. Tồn kho trừ lại 100 cái (về lại 10)
        $this->assertEquals(10, $product->fresh()->stock_quantity);
        // 2. Nợ NCC giảm trừ 200.000đ (về lại 0)
        $this->assertEquals(0, (float) $supplier->fresh()->debt_balance);
        // 3. Trạng thái phiếu là cancelled
        $this->assertEquals('cancelled', $order->fresh()->status);
        $this->assertStringContainsString('Hàng bị lỗi bao bì', $order->fresh()->notes);
    }

    public function test_purchase_order_authorization(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create(['role' => 'cashier']);
        $technician = User::factory()->create(['role' => 'technician']);

        // Admin có quyền truy cập
        $this->actingAs($admin)
            ->get(route('filament.admin.resources.suppliers.index'))
            ->assertSuccessful();

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.purchase-orders.index'))
            ->assertSuccessful();

        // Thu ngân bị chặn
        $this->actingAs($cashier)
            ->get(route('filament.admin.resources.suppliers.index'))
            ->assertForbidden();

        $this->actingAs($cashier)
            ->get(route('filament.admin.resources.purchase-orders.index'))
            ->assertForbidden();

        // Kỹ thuật viên bị chặn
        $this->actingAs($technician)
            ->get(route('filament.admin.resources.suppliers.index'))
            ->assertForbidden();

        $this->actingAs($technician)
            ->get(route('filament.admin.resources.purchase-orders.index'))
            ->assertForbidden();
    }
}
