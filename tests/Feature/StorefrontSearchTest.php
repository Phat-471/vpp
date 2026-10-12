<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_suggestions_returns_matching_active_products(): void
    {
        $category = Category::create([
            'name' => 'Giấy in văn phòng',
            'slug' => 'giay-in-van-phong',
        ]);

        $p1 = Product::create([
            'category_id' => $category->id,
            'name' => 'Giấy in Double A A4 70gsm',
            'slug' => 'giay-in-double-a-a4-70gsm',
            'sku' => 'DA-A4-70',
            'base_unit' => 'ram',
            'retail_price' => 85000,
            'stock_quantity' => 100,
            'is_active' => true,
        ]);

        $p2 = Product::create([
            'category_id' => $category->id,
            'name' => 'Giấy in IK Plus A4 70gsm',
            'slug' => 'giay-in-ik-plus-a4-70gsm',
            'sku' => 'IK-A4-70',
            'base_unit' => 'ram',
            'retail_price' => 82000,
            'stock_quantity' => 50,
            'is_active' => true,
        ]);

        $pInactive = Product::create([
            'category_id' => $category->id,
            'name' => 'Giấy in Bãi Bằng ngưng bán',
            'slug' => 'giay-in-bai-bang-ngung-ban',
            'sku' => 'BB-A4-70',
            'base_unit' => 'ram',
            'retail_price' => 60000,
            'stock_quantity' => 10,
            'is_active' => false,
        ]);

        // Tìm kiếm 'Double'
        $response = $this->getJson(route('storefront.search-suggestions', ['q' => 'Double']));
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            'name' => 'Giấy in Double A A4 70gsm',
            'sku' => 'DA-A4-70',
        ]);

        // Không trả về sản phẩm không hoạt động
        $responseAll = $this->getJson(route('storefront.search-suggestions', ['q' => 'Giấy in']));
        $responseAll->assertOk();
        $responseAll->assertJsonCount(2, 'data');
        $this->assertFalse(collect($responseAll->json('data'))->contains('sku', 'BB-A4-70'));
    }

    public function test_search_suggestions_requires_minimum_length(): void
    {
        $response = $this->getJson(route('storefront.search-suggestions', ['q' => 'a']));
        $response->assertStatus(422);
    }
}
