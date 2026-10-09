<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SpamProtectionTest extends TestCase
{
    use RefreshDatabase;

    private function createProduct(): Product
    {
        return Product::create([
            'sku' => 'TEST-' . Str::random(6),
            'slug' => (string) Str::uuid(),
            'name' => 'Bút bi kiểm thử',
            'base_unit' => 'Cây',
            'retail_price' => 5000,
            'cost_price' => 3000,
            'stock_quantity' => 100,
            'is_active' => true,
        ]);
    }

    private function orderPayload(Product $product, array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Nguyễn Văn Kiểm Thử',
            'customer_phone' => '0912345678',
            'customer_address' => 'Số 123 Đường Trần Phú, Quận 5',
            'payment_method' => 'cod',
            'notes' => 'Giao giờ hành chính',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ], $overrides);
    }

    public function test_honeypot_field_in_checkout_blocks_automated_bot_spam(): void
    {
        $product = $this->createProduct();

        // Bot fills hidden honeypot field
        $payload = $this->orderPayload($product, [
            'website_url' => 'https://spam-bot.site',
        ]);

        $response = $this->postJson('/dat-hang-online', $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('website_url');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_honeypot_empty_allows_legitimate_order_and_records_ip(): void
    {
        $product = $this->createProduct();

        $payload = $this->orderPayload($product, [
            'website_url' => '',
        ]);

        $response = $this->postJson('/dat-hang-online', $payload);

        $response->assertOk();
        $this->assertDatabaseCount('orders', 1);

        $order = Order::first();
        $this->assertNotNull($order->ip_address);
        $this->assertFalse($order->is_suspicious);
    }

    public function test_repeated_checkout_from_same_phone_flags_as_suspicious_order(): void
    {
        $product = $this->createProduct();

        // Đơn 1
        $this->postJson('/dat-hang-online', $this->orderPayload($product))->assertOk();
        // Đơn 2
        $this->postJson('/dat-hang-online', $this->orderPayload($product))->assertOk();
        // Đơn 3 (Liên tiếp cùng SĐT trong 15 phút) -> Flag suspicious
        $this->postJson('/dat-hang-online', $this->orderPayload($product))->assertOk();

        $this->assertDatabaseCount('orders', 3);

        $latestOrder = Order::latest('id')->first();
        $this->assertTrue($latestOrder->is_suspicious);
        $this->assertStringContainsString('15 phút', $latestOrder->suspicious_reason);
    }

    public function test_customer_login_fails_gracefully_without_username_enumeration(): void
    {
        $response = $this->post('/dang-nhap', [
            'phone' => '0999999999',
            'password' => 'wrongpass123',
        ]);

        $response->assertSessionHas('error', 'Số điện thoại hoặc mật khẩu không chính xác.');
    }

    public function test_user_can_access_panel_restricts_unauthorized_roles(): void
    {
        $admin = new User(['role' => 'admin']);
        $cashier = new User(['role' => 'cashier']);
        $tech = new User(['role' => 'technician']);
        $guestUser = new User(['role' => 'customer']);

        $panel = new Panel();
        $panel->id('admin');

        $this->assertTrue($admin->canAccessPanel($panel));
        $this->assertTrue($cashier->canAccessPanel($panel));
        $this->assertTrue($tech->canAccessPanel($panel));
        $this->assertFalse($guestUser->canAccessPanel($panel));
    }
}
