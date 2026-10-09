<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\PhoneOtp;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OtpAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_registration_activates_customer_and_logs_in(): void
    {
        $response = $this->post('/dang-ky', [
            'name' => 'Nguyễn Văn Minh',
            'phone' => '0988776655',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $response->assertRedirect(route('customer.profile'));

        $customer = Customer::where('phone', '0988776655')->first();
        $this->assertNotNull($customer);
        $this->assertTrue($customer->isActivated());
        $this->assertAuthenticatedAs($customer, 'customer');
    }

    public function test_correct_otp_activates_customer_and_logs_in(): void
    {
        $customer = Customer::create([
            'name' => 'Trần Văn Hùng',
            'phone' => '0912345678',
            'phone_last4' => '5678',
            'password' => bcrypt('password123'),
            'phone_verified_at' => null,
        ]);

        $otpService = app(OtpService::class);
        $result = $otpService->generate('0912345678', 'verify', $customer);

        $otpRecord = PhoneOtp::where('phone', '0912345678')->latest('id')->first();
        $this->assertNotNull($otpRecord);

        // Gửi API kiểm tra mã OTP đúng
        $response = $this->postJson(route('otp.verify'), [
            'phone' => '0912345678',
            'otp' => $otpRecord->otp_code,
            'action' => 'verify',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $customer->refresh();
        $this->assertTrue($customer->isActivated());
        $this->assertAuthenticatedAs($customer, 'customer');

        $otpRecord->refresh();
        $this->assertTrue($otpRecord->is_used);
    }

    public function test_incorrect_otp_fails_and_increments_attempts(): void
    {
        $customer = Customer::create([
            'name' => 'Lê Thị Thu',
            'phone' => '0933221100',
            'phone_last4' => '1100',
            'password' => bcrypt('password123'),
            'phone_verified_at' => null,
        ]);

        $otpService = app(OtpService::class);
        $otpService->generate('0933221100', 'verify', $customer);

        // Nhập mã sai
        $response = $this->postJson(route('otp.verify'), [
            'phone' => '0933221100',
            'otp' => '000000',
            'action' => 'verify',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);

        $otpRecord = PhoneOtp::where('phone', '0933221100')->latest('id')->first();
        $this->assertSame(1, $otpRecord->attempts);
        $this->assertFalse($otpRecord->is_used);

        $customer->refresh();
        $this->assertFalse($customer->isActivated());
    }

    public function test_resend_otp_enforces_cooldown_period(): void
    {
        $otpService = app(OtpService::class);
        $first = $otpService->generate('0977889900');
        $this->assertTrue($first['success']);

        // Gửi lại ngay lập tức
        $second = $otpService->generate('0977889900');
        $this->assertFalse($second['success']);
        $this->assertTrue($second['cooldown']);
        $this->assertGreaterThan(0, $second['wait_seconds']);
    }

    public function test_customer_login_logs_in_and_redirects_to_profile(): void
    {
        $customer = Customer::create([
            'name' => 'Khách Trực Tiếp',
            'phone' => '0966554433',
            'phone_last4' => '4433',
            'password' => bcrypt('secret123'),
            'phone_verified_at' => now(),
        ]);

        $response = $this->post('/dang-nhap', [
            'phone' => '0966554433',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('customer.profile'));
        $this->assertAuthenticatedAs($customer, 'customer');
    }
}
