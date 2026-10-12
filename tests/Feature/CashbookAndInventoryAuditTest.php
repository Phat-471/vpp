<?php

namespace Tests\Feature;

use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockCheck;
use App\Models\StockCheckItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\CashbookService;
use App\Services\FinancialReportService;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashbookAndInventoryAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $cashier;
    protected User $technician;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->cashier = User::factory()->create([
            'role' => 'cashier',
        ]);

        $this->technician = User::factory()->create([
            'role' => 'technician',
        ]);
    }

    public function test_cashbook_receipt_decreases_customer_debt_and_rollback_on_cancel(): void
    {
        $customer = Customer::create([
            'name' => 'Công ty Văn Phòng Mới',
            'phone' => '0988776655',
            'type' => 'wholesale',
            'credit_limit' => 10000000,
            'debt_balance' => 5000000,
        ]);

        $service = app(CashbookService::class);

        // Tạo phiếu thu nợ khách hàng 3.000.000đ
        $receipt = $service->createReceipt([
            'category' => 'customer_debt',
            'amount' => 3000000,
            'customer_id' => $customer->id,
            'payment_method' => 'bank_transfer',
            'created_by' => $this->admin->id,
            'notes' => 'Thu đợt 1',
        ]);

        $this->assertStringStartsWith('PT', $receipt->code);
        $this->assertEquals('receipt', $receipt->type);
        $this->assertEquals(3000000, $receipt->amount);

        // Công nợ khách hàng phải giảm còn 2.000.000đ
        $customer->refresh();
        $this->assertEquals(2000000, (float) $customer->debt_balance);

        // Hủy phiếu thu: công nợ phải được hoàn tác lại 5.000.000đ
        $service->cancelTransaction($receipt, 'Nhập nhầm phiếu');
        $customer->refresh();
        $this->assertEquals(5000000, (float) $customer->debt_balance);
        $this->assertEquals('cancelled', $receipt->fresh()->status);
    }

    public function test_cashbook_payment_decreases_supplier_debt_and_rollback_on_cancel(): void
    {
        $supplier = Supplier::create([
            'code' => 'NCC_TEST_01',
            'name' => 'Công ty Giấy Bãi Bằng',
            'debt_balance' => 8000000,
            'is_active' => true,
        ]);

        $service = app(CashbookService::class);

        // Tạo phiếu chi trả nợ NCC 5.000.000đ
        $payment = $service->createPayment([
            'category' => 'supplier_debt',
            'amount' => 5000000,
            'supplier_id' => $supplier->id,
            'payment_method' => 'bank_transfer',
            'created_by' => $this->admin->id,
            'notes' => 'Thanh toán tiền hàng tháng trước',
        ]);

        $this->assertStringStartsWith('PC', $payment->code);
        $this->assertEquals('payment', $payment->type);
        $this->assertEquals(5000000, $payment->amount);

        // Công nợ NCC phải giảm còn 3.000.000đ
        $supplier->refresh();
        $this->assertEquals(3000000, (float) $supplier->debt_balance);

        // Hủy phiếu chi: công nợ NCC phải hoàn tác lại 8.000.000đ
        $service->cancelTransaction($payment, 'Ủy nhiệm chi bị từ chối');
        $supplier->refresh();
        $this->assertEquals(8000000, (float) $supplier->debt_balance);
        $this->assertEquals('cancelled', $payment->fresh()->status);
    }

    public function test_stock_check_balances_inventory_and_rollback_on_cancel(): void
    {
        $product1 = Product::create([
            'name' => 'Bút bi Thiên Long TL-027',
            'slug' => 'but-bi-thien-long-tl-027',
            'sku' => 'BUT-TL027',
            'stock_quantity' => 100,
            'cost_price' => 3000,
            'price' => 5000,
            'is_active' => true,
        ]);

        $product2 = Product::create([
            'name' => 'Tập 96 trang Campus',
            'slug' => 'tap-96-trang-campus',
            'sku' => 'TAP-CAMPUS-96',
            'stock_quantity' => 50,
            'cost_price' => 8000,
            'price' => 12000,
            'is_active' => true,
        ]);

        $stockCheck = StockCheck::create([
            'code' => StockCheck::generateNextCode(),
            'created_by' => $this->admin->id,
            'status' => 'draft',
        ]);

        // Mặt hàng 1: Tồn máy 100, Thực tế đếm 95 (thiếu 5 do mất/rách)
        StockCheckItem::create([
            'stock_check_id' => $stockCheck->id,
            'product_id' => $product1->id,
            'product_name' => $product1->name,
            'unit_name' => 'Cây',
            'system_quantity' => 100,
            'actual_quantity' => 95,
            'cost_price' => 3000,
            'reason' => 'Rách bao bì hư hỏng',
        ]);

        // Mặt hàng 2: Tồn máy 50, Thực tế đếm 52 (thừa 2 khi kiểm kê)
        StockCheckItem::create([
            'stock_check_id' => $stockCheck->id,
            'product_id' => $product2->id,
            'product_name' => $product2->name,
            'unit_name' => 'Quyển',
            'system_quantity' => 50,
            'actual_quantity' => 52,
            'cost_price' => 8000,
            'reason' => 'Thừa kiểm kê',
        ]);

        $stockCheck->refresh();
        $this->assertEquals(2, $stockCheck->total_items);
        $this->assertEquals(2, $stockCheck->difference_items);

        // Duyệt Cân Bằng Kho
        app(InventoryService::class)->completeStockCheck($stockCheck);

        $product1->refresh();
        $product2->refresh();
        $this->assertEquals(95, $product1->stock_quantity);
        $this->assertEquals(52, $product2->stock_quantity);
        $this->assertEquals('completed', $stockCheck->fresh()->status);

        // Hủy phiếu kiểm kê: Tồn kho phải hoàn tác về số lượng trước khi kiểm (100 và 50)
        app(InventoryService::class)->cancelStockCheck($stockCheck, 'Nhập nhầm số liệu');

        $product1->refresh();
        $product2->refresh();
        $this->assertEquals(100, $product1->stock_quantity);
        $this->assertEquals(50, $product2->stock_quantity);
        $this->assertEquals('cancelled', $stockCheck->fresh()->status);
    }

    public function test_financial_report_service_calculates_profit_and_cogs_accurately(): void
    {
        $product = Product::create([
            'name' => 'Giấy A4 Double A 70gsm',
            'slug' => 'giay-a4-double-a-70gsm',
            'sku' => 'GIAY-AA-70',
            'stock_quantity' => 200,
            'cost_price' => 70000,
            'price' => 95000,
            'is_active' => true,
        ]);

        // Tạo đơn hàng bán 10 ram giấy giá 95.000đ = 950.000đ, vốn 70.000đ * 10 = 700.000đ
        $order = Order::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'order_code' => 'DH_TEST_01',
            'status' => 'completed',
            'subtotal' => 950000,
            'grand_total' => 950000,
            'paid_amount' => 950000,
            'payment_status' => 'paid',
            'created_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_name' => 'Ram',
            'quantity' => 10,
            'cost_price' => 70000,
            'unit_price' => 95000,
            'subtotal' => 950000,
        ]);

        // Tạo 1 khoản chi tiền điện 100.000đ trong Sổ Quỹ
        app(CashbookService::class)->createPayment([
            'category' => 'utilities',
            'amount' => 100000,
            'payment_method' => 'cash',
            'created_by' => $this->admin->id,
            'notes' => 'Tiền điện tháng này',
        ]);

        $reportService = app(FinancialReportService::class);
        $data = $reportService->getProfitReport(now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d'));

        // Doanh thu: 950.000đ
        $this->assertEquals(950000, $data['summary']['total_revenue']);
        // Giá vốn: 700.000đ
        $this->assertEquals(700000, $data['summary']['total_cogs']);
        // Lợi nhuận gộp: 950.000 - 700.000 = 250.000đ
        $this->assertEquals(250000, $data['summary']['gross_profit']);
        // Chi phí vận hành: 100.000đ
        $this->assertEquals(100000, $data['summary']['operating_expenses']);
        // Lợi nhuận thuần: 250.000 - 100.000 = 150.000đ
        $this->assertEquals(150000, $data['summary']['net_profit']);

        // Top sản phẩm
        $this->assertNotEmpty($data['top_products']);
        $this->assertEquals('Giấy A4 Double A 70gsm', $data['top_products'][0]['product_name']);
        $this->assertEquals(250000, (float) $data['top_products'][0]['gross_profit']);
    }

    public function test_cashier_and_technician_authorization_matrix(): void
    {
        // Technician không được xem Sổ quỹ
        $this->actingAs($this->technician);
        $this->assertFalse(\App\Filament\Resources\CashTransactionResource::canViewAny());

        // Cashier được xem và tạo Sổ quỹ nhưng không được xóa
        $this->actingAs($this->cashier);
        $this->assertTrue(\App\Filament\Resources\CashTransactionResource::canViewAny());
        $this->assertTrue(\App\Filament\Resources\CashTransactionResource::canCreate());

        $transaction = CashTransaction::create([
            'code' => 'PT_TEST',
            'type' => 'receipt',
            'category' => 'other_receipt',
            'amount' => 100000,
            'performed_at' => now(),
        ]);
        $this->assertFalse(\App\Filament\Resources\CashTransactionResource::canDelete($transaction));

        // Admin có toàn quyền
        $this->actingAs($this->admin);
        $this->assertTrue(\App\Filament\Resources\CashTransactionResource::canDelete($transaction));
    }
}
