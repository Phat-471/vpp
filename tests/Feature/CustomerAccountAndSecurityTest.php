<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\RepairTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAccountAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_login_with_remember_me(): void
    {
        $customer = Customer::create([
            'name' => 'Nguyễn Thị Hoa',
            'phone' => '0912345678',
            'phone_last4' => '5678',
            'password' => bcrypt('matkhau123'),
            'phone_verified_at' => now(),
        ]);

        $response = $this->post('/dang-nhap', [
            'phone' => '0912345678',
            'password' => 'matkhau123',
            'remember' => '1',
        ]);

        $response->assertRedirect(route('customer.profile'));
        $this->assertAuthenticatedAs($customer, 'customer');

        $customer->refresh();
        $this->assertNotNull($customer->remember_token);
    }

    public function test_customer_forgot_password_successfully_resets_and_logs_in(): void
    {
        $customer = Customer::create([
            'name' => 'Lê Quốc Tuấn',
            'phone' => '0977889911',
            'phone_last4' => '9911',
            'password' => bcrypt('oldpass123'),
            'phone_verified_at' => now(),
        ]);

        $response = $this->post('/quen-mat-khau', [
            'phone' => '0977889911',
            'new_password' => 'newpass456',
            'new_password_confirmation' => 'newpass456',
        ]);

        $response->assertRedirect(route('customer.profile'));
        $this->assertAuthenticatedAs($customer, 'customer');

        $customer->refresh();
        $this->assertTrue(Hash::check('newpass456', $customer->password));
    }

    public function test_customer_forgot_password_fails_for_unregistered_phone(): void
    {
        $response = $this->post('/quen-mat-khau', [
            'phone' => '0900000000',
            'new_password' => 'newpass456',
            'new_password_confirmation' => 'newpass456',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest('customer');
    }

    public function test_customer_change_password_in_profile(): void
    {
        $customer = Customer::create([
            'name' => 'Phạm Minh Trí',
            'phone' => '0933445566',
            'phone_last4' => '5566',
            'password' => bcrypt('currentPass123'),
            'phone_verified_at' => now(),
        ]);

        $response = $this->actingAs($customer, 'customer')
            ->post('/tai-khoan/doi-mat-khau', [
                'current_password' => 'currentPass123',
                'new_password' => 'superSecurePass999',
                'new_password_confirmation' => 'superSecurePass999',
            ]);

        $response->assertSessionHas('success');
        $customer->refresh();
        $this->assertTrue(Hash::check('superSecurePass999', $customer->password));
    }

    public function test_customer_profile_renders_mau_a_tabs(): void
    {
        $customer = Customer::create([
            'name' => 'Công Ty ABC',
            'phone' => '0944556677',
            'phone_last4' => '6677',
            'password' => bcrypt('password123'),
            'phone_verified_at' => now(),
        ]);

        $response = $this->actingAs($customer, 'customer')->get('/tai-khoan');

        $response->assertOk();
        $response->assertSee('Đơn Hàng Của Tôi');
        $response->assertSee('Phiếu Sửa Chữa Máy In');
        $response->assertSee('Đổi Mật Khẩu');
        $response->assertSee('Tài khoản chính thức');
    }
}
