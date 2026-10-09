<?php

namespace Tests\Feature;

use App\Filament\Resources\PrinterModelResource;
use App\Filament\Resources\PrinterModelResource\Pages\CreatePrinterModel;
use App\Filament\Resources\PrinterModelResource\Pages\EditPrinterModel;
use App\Filament\Resources\PrinterModelResource\Pages\ListPrinterModels;
use App\Filament\Resources\PrinterModelResource\RelationManagers\ProductsRelationManager;
use App\Models\Category;
use App\Models\PrinterModel;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PrinterModelResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_access_printer_model_list_and_tabs(): void
    {
        PrinterModel::create([
            'brand' => 'Canon',
            'model_name' => 'LBP 2900',
            'printer_type' => 'Laser đen trắng đơn năng',
            'compatible_cartridges' => 'Cartridge 303, Cartridge 12A',
        ]);

        PrinterModel::create([
            'brand' => 'HP',
            'model_name' => 'LaserJet 107a',
            'printer_type' => 'Laser đen trắng đơn năng',
            'compatible_cartridges' => 'HP 107A (W1107A)',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/printer-models');
        $response->assertStatus(200);
        $response->assertSee('Canon');
        $response->assertSee('LBP 2900');
        $response->assertSee('HP');
        $response->assertSee('LaserJet 107a');

        // Test Livewire tabs
        Livewire::actingAs($this->admin)
            ->test(ListPrinterModels::class)
            ->assertSuccessful()
            ->set('activeTab', 'canon')
            ->assertSee('LBP 2900')
            ->assertDontSee('LaserJet 107a');
    }

    public function test_admin_can_create_printer_model(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CreatePrinterModel::class)
            ->fillForm([
                'brand' => 'Brother',
                'model_name' => 'HL-L2321D',
                'printer_type' => 'Laser đen trắng đơn năng',
                'compatible_cartridges' => 'TN-2385 (Hộp mực) / DR-2385 (Cụm trống)',
                'notes' => 'Cần reset bánh răng khi nạp mực mới',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('printer_models', [
            'brand' => 'Brother',
            'model_name' => 'HL-L2321D',
            'compatible_cartridges' => 'TN-2385 (Hộp mực) / DR-2385 (Cụm trống)',
        ]);
    }

    public function test_admin_can_attach_and_detach_compatible_product_via_relation_manager(): void
    {
        $printer = PrinterModel::create([
            'brand' => 'Canon',
            'model_name' => 'LBP 2900',
            'printer_type' => 'Laser đen trắng đơn năng',
            'compatible_cartridges' => 'Cartridge 303, Cartridge 12A',
        ]);

        $category = Category::create([
            'name' => 'Hộp mực máy in',
            'slug' => 'hop-muc-may-in',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'MUC-12A-01',
            'name' => 'Hộp mực Cartridge 12A / 303 Chuẩn',
            'slug' => 'hop-muc-cartridge-12a-303-chuan',
            'cost_price' => 120000,
            'retail_price' => 220000,
            'stock_quantity' => 15,
            'is_active' => true,
        ]);

        // Kiểm tra gắn sản phẩm vào máy in
        $printer->products()->attach($product->id, ['notes' => 'Lắp vừa vặn 100%']);

        $this->assertEquals(1, $printer->products()->count());
        $this->assertTrue($printer->products->contains($product));

        // Test Livewire relation manager
        Livewire::actingAs($this->admin)
            ->test(ProductsRelationManager::class, [
                'ownerRecord' => $printer,
                'pageClass' => EditPrinterModel::class,
            ])
            ->assertSuccessful()
            ->assertSee('MUC-12A-01')
            ->assertSee('Hộp mực Cartridge 12A / 303 Chuẩn')
            ->assertSee('15 Cái')
            ->assertSee('Lắp vừa vặn 100%');
    }
}
