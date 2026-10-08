<?php

namespace Tests\Feature;

use App\Livewire\PosTerminal;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutValidationTest extends TestCase
{
    use RefreshDatabase;

    private function product(): Product
    {
        return Product::create(['sku' => 'TEST-'.Str::random(6), 'slug' => (string) Str::uuid(), 'name' => 'Giấy kiểm thử', 'base_unit' => 'Ram', 'retail_price' => 75000, 'cost_price' => 50000, 'stock_quantity' => 10, 'is_active' => true]);
    }

    private function payload(Product $product): array
    {
        return ['customer_name' => 'Người mua kiểm thử', 'customer_phone' => '0912345678', 'customer_address' => 'Địa chỉ kiểm thử', 'payment_method' => 'cod', 'items' => [['product_id' => $product->id, 'quantity' => 1]]];
    }

    public function test_invalid_receiver_formats_are_rejected_before_any_writes(): void
    {
        $product = $this->product();
        foreach ([['customer_name', 'á'], ['customer_name', '12345'], ['customer_phone', 'dás'], ['customer_phone', '09abc12345678'], ['customer_phone', '1234567890'], ['customer_address', 'ádas'], ['customer_address', '123456'], ['customer_address', '   ']] as [$field, $value]) {
            $this->postJson('/dat-hang-online', array_merge($this->payload($product), [$field => $value]))->assertUnprocessable()->assertJsonValidationErrors($field);
        }
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('customers', 0);
        $this->assertSame(10, $product->refresh()->stock_quantity);
    }

    public function test_valid_formatted_phone_is_normalized_before_storing(): void
    {
        $product = $this->product();
        $this->postJson('/dat-hang-online', array_merge($this->payload($product), ['customer_phone' => '+84 912 345 678']))->assertOk();
        $this->assertSame('0912345678', Order::firstOrFail()->customer_phone);
    }

    public function test_invoice_formats_are_enforced_even_when_native_validation_is_bypassed(): void
    {
        $product = $this->product();
        $invoice = ['is_vat_invoice' => true, 'company_tax_id' => '0123456789', 'company_name' => 'Người mua kiểm thử', 'company_address' => 'Địa chỉ kiểm thử', 'invoice_email' => 'invoice@example.test'];
        foreach ([['company_tax_id', '123'], ['company_tax_id', '12x3456789'], ['company_name', ' '], ['company_address', '12345'], ['invoice_email', 'invalid'], ['invoice_email', 'a@b']] as [$field, $value]) {
            $this->postJson('/dat-hang-online', array_merge($this->payload($product), $invoice, [$field => $value]))->assertUnprocessable()->assertJsonValidationErrors($field);
        }
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(10, $product->refresh()->stock_quantity);
    }

    public function test_personal_twelve_digit_code_is_manual_and_never_sent_to_company_provider(): void
    {
        Http::preventStrayRequests();
        $this->postJson(route('business.lookup'), ['tax_code' => '123456789012'])->assertStatus(503)->assertSee('12');
        Http::assertNothingSent();
        $product = $this->product();
        $this->postJson('/dat-hang-online', array_merge($this->payload($product), [
            'is_vat_invoice' => true, 'company_tax_id' => '123456789012', 'company_name' => 'Người mua kiểm thử', 'company_address' => 'Địa chỉ kiểm thử', 'invoice_email' => 'invoice@example.test',
        ]))->assertOk();
        $this->assertSame('123456789012', Order::firstOrFail()->company_tax_id);
    }

    public function test_pos_cannot_bypass_customer_phone_format(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $product = $this->product();
        Livewire::test(PosTerminal::class)->call('addToCart', $product->id)->set('cashGiven', '100000')
            ->set('customerPhone', '09abc12345678')->call('checkout')->assertHasErrors('customer_phone');
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(10, $product->refresh()->stock_quantity);
    }
}
