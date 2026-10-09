<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\RepairTicket;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepairTicketPrintTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set('site_name', 'VPP & Máy In Ánh Dương');
        Setting::set('hotline', '0912.345.678');
        Setting::set('address', '123 Đường Công Nghệ, Quận 1');
        Setting::set('warranty_policy', 'Bảo hành 30 ngày cho linh kiện thay thế');
    }

    public function test_can_view_a4_a5_repair_ticket_print(): void
    {
        $customer = Customer::create([
            'name' => 'Nguyễn Văn Minh',
            'phone' => '0988776655',
            'phone_last4' => '6655',
            'address' => '456 Phố Mới',
        ]);

        $ticket = RepairTicket::create([
            'ticket_code' => 'SC-20261009-001',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'phone_last4' => '6655',
            'device_name' => 'Canon LBP 2900',
            'serial_number' => 'CN2900-9988',
            'accessories' => 'Dây nguồn, Cáp USB',
            'issue_description' => 'Kẹt giấy liên tục khi in từ khay 1',
            'technician_diagnosis' => 'Rách bao lụa sấy, mòn cao su kéo giấy',
            'intake_flow' => 'quote_immediate',
            'status' => 'received',
            'labor_fee' => 100000,
            'parts_total' => 250000,
            'grand_total' => 350000,
            'paid_amount' => 50000,
            'payment_status' => 'partially_paid',
            'promised_at' => now()->addDay(),
        ]);

        $response = $this->get(route('print.repair-ticket', $ticket->id));

        $response->assertOk();
        $response->assertSee('SC-20261009-001');
        $response->assertSee('Nguyễn Văn Minh');
        $response->assertSee('Canon LBP 2900');
        $response->assertSee('VPP & Máy In Ánh Dương');
        $response->assertSee('0912.345.678');
        $response->assertSee('CUỐNG DÁN LÊN THÂN MÁY');
        $response->assertSee('PHIẾU HẸN TIẾP NHẬN MÁY');
        $response->assertSee(route('print.repair-ticket-sticker', $ticket->id));
    }

    public function test_can_view_decal_sticker_print(): void
    {
        $customer = Customer::create([
            'name' => 'Trần Thị Thu',
            'phone' => '0901234567',
            'phone_last4' => '4567',
            'address' => '789 Đường Lê Lợi',
        ]);

        $ticket = RepairTicket::create([
            'ticket_code' => 'SC-20261009-002',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'phone_last4' => '4567',
            'device_name' => 'Brother HL-L2321D',
            'serial_number' => 'BR2321-7766',
            'accessories' => 'Không kèm phụ kiện',
            'issue_description' => 'Bản in mờ, sọc đen',
            'status' => 'diagnosing',
            'labor_fee' => 0,
            'parts_total' => 0,
            'grand_total' => 0,
            'paid_amount' => 0,
            'payment_status' => 'unpaid',
            'promised_at' => now()->addDay(),
        ]);

        $response = $this->get(route('print.repair-ticket-sticker', $ticket->id));

        $response->assertOk();
        $response->assertSee('SC-20261009-002');
        $response->assertSee('Trần Thị Thu');
        $response->assertSee('Brother HL-L2321D');
        $response->assertSee('4567');
        $response->assertSee('api.qrserver.com');
    }

    public function test_print_returns_404_when_ticket_not_found(): void
    {
        $this->get('/print/repair-ticket/999999')->assertNotFound();
        $this->get('/print/repair-ticket-sticker/999999')->assertNotFound();
    }
}
