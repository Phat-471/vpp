<?php

namespace Tests\Feature;

use App\Livewire\PosTerminal;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\InvoiceDetails;
use App\Services\PosShiftService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class BusinessTaxLookupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.tax_lookup.provider' => 'vietqr']);
        Http::preventStrayRequests();
    }

    private function fake(string $tax = '0123456789'): void
    {
        Http::fake(['api.vietqr.io/*' => Http::response(['code' => '00', 'data' => ['id' => $tax, 'name' => 'Doanh nghiệp kiểm thử', 'address' => 'Địa chỉ kiểm thử']])]);
    }

    public function test_lookup_is_validated_cached_and_returns_only_public_fields(): void
    {
        $this->fake();
        $this->postJson(route('business.lookup'), ['tax_code' => '0123456789'])->assertOk()->assertJsonPath('data.name', 'Doanh nghiệp kiểm thử');
        $this->postJson(route('business.lookup'), ['tax_code' => '0123456789'])->assertOk();
        Http::assertSentCount(1);
        $this->postJson(route('business.lookup'), ['tax_code' => ['invalid']])->assertUnprocessable();
        $this->postJson(route('business.lookup'), ['tax_code' => 'https://localhost'])->assertUnprocessable();
        Http::assertSentCount(1);
    }

    public function test_branch_code_preserves_leading_zero_and_is_normalized(): void
    {
        $this->fake('0123456789-001');
        $this->postJson(route('business.lookup'), ['tax_code' => '0123456789001'])->assertOk()->assertJsonPath('data.tax_code', '0123456789-001');
        Http::assertSent(fn ($r) => str_ends_with($r->url(), '/0123456789-001'));
    }

    public function test_failure_and_mismatched_results_never_fill_a_company(): void
    {
        Http::fake(['*' => Http::response(['code' => '00', 'data' => ['id' => '9999999999', 'name' => 'Sai doanh nghiệp', 'address' => 'Sai địa chỉ']])]);
        $this->postJson(route('business.lookup'), ['tax_code' => '0123456789'])->assertStatus(503)->assertDontSee('Sai doanh nghiệp');
        Http::fake(['*' => Http::response('Sensitive provider diagnostic', 500)]);
        $this->postJson(route('business.lookup'), ['tax_code' => '0123456789'])->assertStatus(503)->assertDontSee('Sensitive provider diagnostic');
    }

    public function test_not_found_is_short_cached_and_provider_has_a_request_limit(): void
    {
        Http::fake(['*' => Http::response(['code' => '01', 'data' => null])]);
        $this->postJson(route('business.lookup'), ['tax_code' => '0123456789'])->assertNotFound();
        $this->postJson(route('business.lookup'), ['tax_code' => '0123456789'])->assertNotFound();
        Http::assertSentCount(1);
        for ($i = 0; $i < 9; $i++) {
            $this->postJson(route('business.lookup'), ['tax_code' => '123456789'.$i])->assertNotFound();
        }
        $this->postJson(route('business.lookup'), ['tax_code' => '1234567800'])->assertStatus(503);
        Http::assertSentCount(10);
    }

    public function test_xinvoice_adapter_and_vietqr_retirement(): void
    {
        config(['services.tax_lookup.provider' => 'xinvoice', 'services.tax_lookup.client_id' => 'test-client', 'services.tax_lookup.api_key' => 'test-key']);
        Http::fake(['api.xinvoice.vn/*' => Http::response(['taxID' => '0123456789', 'name' => 'Tên kiểm thử', 'address' => 'Địa chỉ kiểm thử'])]);
        $this->postJson(route('business.lookup'), ['tax_code' => '0123456789'])->assertOk()->assertJsonPath('data.source', 'xinvoice');
        Http::assertSent(fn ($r) => $r->hasHeader('client-id', 'test-client') && $r->hasHeader('api-key', 'test-key'));
        config(['services.tax_lookup.provider' => 'vietqr']);
        $this->travelTo(now()->setDate(2027, 3, 2));
        $this->postJson(route('business.lookup'), ['tax_code' => '0123456789'])->assertStatus(503);
        Http::assertSentCount(1);
    }

    private function product(): Product
    {
        return Product::create(['sku' => 'TEST-'.Str::random(6), 'slug' => (string) Str::uuid(), 'name' => 'Giấy kiểm thử', 'base_unit' => 'Ram', 'retail_price' => 75000, 'cost_price' => 50000, 'stock_quantity' => 10, 'is_active' => true]);
    }

    public function test_pos_autofills_clears_old_company_and_saves_invoice_details(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        app(PosShiftService::class)->open(auth()->user(), 0);
        $this->fake();
        $product = $this->product();
        $component = Livewire::test(PosTerminal::class)->set('isVatInvoice', true)->set('companyTaxId', '0123456789')
            ->assertSet('companyName', 'Doanh nghiệp kiểm thử')->assertSet('companyAddress', 'Địa chỉ kiểm thử');
        $component->set('companyTaxId', '012')->assertSet('companyName', '')->assertSet('companyAddress', '');
        $component->set('companyTaxId', '0123456789')->set('invoiceEmail', 'invoice@example.test')
            ->call('addToCart', $product->id)->set('cashGiven', '100000')->call('checkout')->assertHasNoErrors();
        $order = Order::firstOrFail();
        $this->assertTrue($order->is_vat_invoice);
        $this->assertSame('0123456789', $order->company_tax_id);
        $this->assertSame('invoice@example.test', $order->invoice_email);
        $this->get(route('pos.receipt', $order->uuid))->assertSee('Doanh nghiệp kiểm thử');
    }

    public function test_missing_invoice_details_block_order_and_disabled_invoice_is_cleared(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $product = $this->product();
        Livewire::test(PosTerminal::class)->set('isVatInvoice', true)->call('addToCart', $product->id)
            ->set('cashGiven', '100000')->call('checkout')->assertHasErrors(['company_tax_id', 'invoice_email']);
        $this->assertDatabaseCount('orders', 0);
        $data = app(InvoiceDetails::class)->validate(['is_vat_invoice' => false, 'company_name' => 'Stale company']);
        $this->assertNull($data['company_name']);
        $this->assertSame(10, $product->refresh()->stock_quantity);
    }

    public function test_checkout_and_shared_drawer_use_same_invoice_component(): void
    {
        $this->get('/thanh-toan')->assertOk()->assertSee('checkout-invoice')->assertSee('drawer-invoice')->assertSee('invoice-form.js');
        $this->get('/san-pham')->assertOk()->assertSee('drawer-invoice');
        $product = $this->product();
        $payload = ['customer_name' => 'Khách kiểm thử', 'customer_phone' => '0900000000', 'customer_address' => 'Địa chỉ kiểm thử', 'payment_method' => 'cod', 'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'is_vat_invoice' => true, 'company_tax_id' => '0123456789', 'company_name' => 'Doanh nghiệp kiểm thử', 'company_address' => 'Địa chỉ doanh nghiệp', 'invoice_email' => 'invoice@example.test'];
        $this->postJson('/dat-hang-online', array_merge($payload, ['invoice_email' => 'invalid']))->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0);
        $this->postJson('/dat-hang-online', $payload)->assertOk();
        $this->assertSame('invoice@example.test', Order::firstOrFail()->invoice_email);
    }
}
