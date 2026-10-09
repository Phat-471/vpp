<?php

namespace Tests\Feature;

use App\Filament\Pages\FinancialReportPage;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\RepairItem;
use App\Models\RepairTicket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinancialReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_access_financial_report_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/financial-report-page');
        $response->assertStatus(200);
        $response->assertSee('Báo Cáo Doanh Thu', false);
        $response->assertSee('Tổng Doanh Thu Hợp Nhất', false);
    }

    public function test_guest_cannot_access_financial_report_page(): void
    {
        $response = $this->get('/admin/financial-report-page');
        $response->assertRedirect('/admin/login');
    }

    public function test_period_switching_updates_dates_and_tabs(): void
    {
        Livewire::actingAs($this->admin)
            ->test(FinancialReportPage::class)
            ->assertSet('period', 'this_month')
            ->call('setPeriod', 'today')
            ->assertSet('period', 'today')
            ->assertSet('startDate', now()->startOfDay()->format('Y-m-d'))
            ->call('setPeriod', 'last_7_days')
            ->assertSet('period', 'last_7_days')
            ->call('setActiveTab', 'vat_table')
            ->assertSet('activeTab', 'vat_table')
            ->call('setActiveTab', 'breakdown')
            ->assertSet('activeTab', 'breakdown');
    }

    public function test_consolidated_revenue_includes_orders_and_repair_tickets(): void
    {
        $customer = Customer::create([
            'name' => 'Khách Hàng Báo Cáo',
            'phone' => '0988776655',
            'phone_last4' => '6655',
        ]);

        $product = Product::create([
            'sku' => 'PROD-REP-1',
            'name' => 'Ram Giấy Double A',
            'slug' => 'ram-giay-double-a',
            'cost_price' => 50000,
            'sale_price' => 70000,
            'stock_quantity' => 100,
            'is_active' => true,
        ]);

        // 1. Tạo Đơn hàng bán VPP
        $order = Order::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'channel' => 'pos',
            'status' => 'completed',
            'subtotal' => 140000,
            'discount_amount' => 10000,
            'tax_rate' => 8,
            'tax_amount' => 10400,
            'grand_total' => 140400,
            'paid_amount' => 140400,
            'payment_status' => 'paid',
            'payment_method' => 'vietqr',
            'is_vat_invoice' => true,
            'company_name' => 'Công Ty Test VAT',
            'company_tax_id' => '0102030405',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_name' => 'Ram',
            'quantity' => 2,
            'cost_price' => 50000,
            'unit_price' => 70000,
            'subtotal' => 140000,
            'conversion_rate' => 1,
        ]);

        // 2. Tạo Phiếu sửa chữa máy in
        $repair = RepairTicket::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'device_name' => 'Canon 2900',
            'issue_description' => 'Kẹt giấy liên tục',
            'status' => 'completed',
            'labor_fee' => 100000,
            'parts_total' => 150000,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'grand_total' => 250000,
            'paid_amount' => 250000,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
        ]);

        RepairItem::create([
            'repair_ticket_id' => $repair->id,
            'product_id' => $product->id,
            'item_name' => 'Thay Trống Canon 2900',
            'quantity' => 1,
            'cost_price' => 60000,
            'unit_price' => 150000,
            'subtotal' => 150000,
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(FinancialReportPage::class)
            ->set('period', 'today');

        $reportData = $component->get('reportData');

        // Tổng doanh thu phải hợp nhất: 140.400đ (Order) + 250.000đ (Repair) = 390.400đ
        $this->assertEquals(390400, $reportData['total_revenue']);
        $this->assertEquals(140400, $reportData['order_revenue']);
        $this->assertEquals(250000, $reportData['repair_revenue']);
        $this->assertEquals(1, $reportData['order_count']);
        $this->assertEquals(1, $reportData['repair_count']);
        $this->assertEquals(10400, $reportData['total_vat']);
    }

    public function test_export_tax_csv_generates_stream_with_utf8_bom(): void
    {
        $customer = Customer::create([
            'name' => 'Khách Test CSV',
            'phone' => '0911223344',
            'phone_last4' => '3344',
        ]);

        Order::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'channel' => 'pos',
            'status' => 'completed',
            'subtotal' => 100000,
            'discount_amount' => 0,
            'tax_rate' => 8,
            'tax_amount' => 8000,
            'grand_total' => 108000,
            'paid_amount' => 108000,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'is_vat_invoice' => true,
            'company_name' => 'Công Ty Minh Họa CSV',
            'company_tax_id' => '0312345678',
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(FinancialReportPage::class)
            ->set('period', 'this_month');

        $response = $component->instance()->exportTaxCsv();

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class, $response);
        $this->assertEquals('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));

        // Kiểm tra nội dung stream có BOM UTF-8
        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $csvContent);
        $this->assertStringContainsString('BẢNG KÊ HÓA ĐƠN & THUẾ GIÁ TRỊ GIA TĂNG (GTGT)', $csvContent);
        $this->assertStringContainsString('Công Ty Minh Họa CSV', $csvContent);
        $this->assertStringContainsString('0312345678', $csvContent);
    }
}
