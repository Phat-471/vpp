<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\RepairTicket;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class WebhookReconciliationTest extends TestCase
{
    use RefreshDatabase;

    private function createSampleOrder(array $attributes = []): Order
    {
        $customer = Customer::create([
            'name' => 'Nguyễn Văn Nam',
            'phone' => '0988776655',
            'phone_last4' => '6655',
            'address' => '45 Tràng Tiền, Hoàn Kiếm, Hà Nội',
        ]);

        return Order::create(array_merge([
            'uuid' => (string) Str::uuid(),
            'order_code' => 'HD260088',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'channel' => 'web',
            'status' => 'pending',
            'subtotal' => 200000,
            'grand_total' => 200000,
            'paid_amount' => 0,
            'payment_status' => 'unpaid',
            'payment_method' => 'vietqr',
        ], $attributes));
    }

    private function createSampleRepairTicket(array $attributes = []): RepairTicket
    {
        $customer = Customer::create([
            'name' => 'Công Ty Máy Văn Phòng Sao Mai',
            'phone' => '0912888999',
            'phone_last4' => '8999',
            'address' => '78 Hai Bà Trưng, Hà Nội',
        ]);

        return RepairTicket::create(array_merge([
            'uuid' => (string) Str::uuid(),
            'ticket_code' => 'SC260055',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'phone_last4' => '8999',
            'device_name' => 'Máy in Canon LBP 2900',
            'issue_description' => 'Kẹt giấy liên tục',
            'status' => 'received',
            'labor_fee' => 350000,
            'grand_total' => 350000,
            'paid_amount' => 0,
            'payment_status' => 'unpaid',
            'payment_method' => 'vietqr',
        ], $attributes));
    }

    public function test_sepay_webhook_matches_and_marks_order_as_paid_and_processing(): void
    {
        $order = $this->createSampleOrder();

        $response = $this->postJson('/api/webhook/sepay', [
            'id' => 'SP_TX_123456',
            'gateway' => 'MBBank',
            'transferAmount' => 200000,
            'content' => 'HD260088 chuyen tien mua giay A4',
            'accountNumber' => '190333888999',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'matched' => true,
                'reference_code' => 'HD260088',
                'amount' => 200000,
            ]);

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('processing', $order->status);
        $this->assertEquals(200000, $order->paid_amount);

        $this->assertDatabaseHas('payment_transactions', [
            'gateway' => 'sepay',
            'transaction_id' => 'SP_TX_123456',
            'reference_code' => 'HD260088',
            'order_id' => $order->id,
            'status' => 'processed',
        ]);
    }

    public function test_sepay_webhook_is_idempotent_and_ignores_duplicate_transactions(): void
    {
        $order = $this->createSampleOrder();

        $payload = [
            'id' => 'SP_TX_DUPLICATE_01',
            'gateway' => 'MBBank',
            'transferAmount' => 200000,
            'content' => 'HD260088 chuyen tien VPP',
            'accountNumber' => '190333888999',
        ];

        // Lần gọi 1
        $res1 = $this->postJson('/api/webhook/sepay', $payload);
        $res1->assertOk()->assertJson(['success' => true, 'matched' => true]);

        $this->assertEquals(200000, $order->fresh()->paid_amount);

        // Lần gọi 2 (Giả lập SePay gửi lại cùng ID)
        $res2 = $this->postJson('/api/webhook/sepay', $payload);
        $res2->assertOk()->assertJson([
            'success' => true,
            'duplicate' => true,
        ]);

        // Số tiền không bị cộng dồn thành 400.000
        $this->assertEquals(200000, $order->fresh()->paid_amount);
        $this->assertSame(1, PaymentTransaction::where('transaction_id', 'SP_TX_DUPLICATE_01')->count());
    }

    public function test_sepay_webhook_rejects_unauthorized_when_token_configured(): void
    {
        Setting::set('sepay_webhook_token', 'SECRET_SEPAY_KEY_789');

        $payload = [
            'id' => 'SP_TX_AUTH_TEST',
            'transferAmount' => 100000,
            'content' => 'HD260088 thanh toan',
        ];

        // 1. Không gửi token -> 401
        $this->postJson('/api/webhook/sepay', $payload)->assertStatus(401);

        // 2. Gửi sai token -> 401
        $this->withHeaders(['Authorization' => 'Apikey WRONG_KEY'])
            ->postJson('/api/webhook/sepay', $payload)
            ->assertStatus(401);

        // 3. Gửi đúng token -> 200
        $this->withHeaders(['Authorization' => 'Apikey SECRET_SEPAY_KEY_789'])
            ->postJson('/api/webhook/sepay', $payload)
            ->assertOk();
    }

    public function test_casso_webhook_matches_repair_ticket(): void
    {
        $ticket = $this->createSampleRepairTicket();

        $response = $this->postJson('/api/webhook/casso', [
            'error' => 0,
            'data' => [
                [
                    'tid' => 'CASSO_TID_9999',
                    'description' => 'Thanh toan phi sua may in SC260055',
                    'amount' => 350000,
                    'bank_sub_acc_id' => '190333888999',
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJson([
                'error' => 0,
                'processed_count' => 1,
            ]);

        $ticket->refresh();
        $this->assertSame('paid', $ticket->payment_status);
        $this->assertEquals(350000, $ticket->paid_amount);

        $this->assertDatabaseHas('payment_transactions', [
            'gateway' => 'casso',
            'transaction_id' => 'CASSO_TID_9999',
            'reference_code' => 'SC260055',
            'repair_ticket_id' => $ticket->id,
            'status' => 'processed',
        ]);
    }

    public function test_casso_webhook_is_idempotent(): void
    {
        $ticket = $this->createSampleRepairTicket();

        $payload = [
            'data' => [
                [
                    'tid' => 'CASSO_TID_REPLAY',
                    'description' => 'SC260055 nap muc in',
                    'amount' => 350000,
                ],
            ],
        ];

        // Lần 1
        $this->postJson('/api/webhook/casso', $payload)->assertOk()->assertJson(['processed_count' => 1]);
        $this->assertEquals(350000, $ticket->fresh()->paid_amount);

        // Lần 2 gửi lại cùng tid
        $this->postJson('/api/webhook/casso', $payload)->assertOk()->assertJson([
            'processed_count' => 0,
            'duplicate_count' => 1,
        ]);
        $this->assertEquals(350000, $ticket->fresh()->paid_amount);
    }

    public function test_casso_webhook_rejects_unauthorized_when_token_configured(): void
    {
        Setting::set('casso_webhook_token', 'MY_CASSO_SECURE_TOKEN');

        $payload = [
            'data' => [
                ['tid' => 'CASSO_AUTH_TEST', 'amount' => 50000, 'description' => 'HD260088 test'],
            ],
        ];

        // Không gửi token -> 401
        $this->postJson('/api/webhook/casso', $payload)->assertStatus(401);

        // Gửi header secure-token đúng -> 200
        $this->withHeaders(['secure-token' => 'MY_CASSO_SECURE_TOKEN'])
            ->postJson('/api/webhook/casso', $payload)
            ->assertOk();
    }
}
