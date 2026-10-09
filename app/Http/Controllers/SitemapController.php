<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Tự động sinh XML Sitemap 0.9 chuẩn Google & Bing
     */
    public function index(): Response
    {
        $urls = [];

        // 1. Trang tĩnh cốt lõi
        $urls[] = [
            'loc' => route('storefront.index'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];
        $urls[] = [
            'loc' => route('storefront.products'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '0.9',
        ];
        $urls[] = [
            'loc' => route('storefront.wholesale'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ];
        $urls[] = [
            'loc' => route('lookup.index'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ];
        $urls[] = [
            'loc' => route('storefront.about'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.7',
        ];
        $urls[] = [
            'loc' => route('storefront.terms'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.5',
        ];
        $urls[] = [
            'loc' => route('storefront.privacy'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.5',
        ];

        // 2. Danh mục sản phẩm (Category URLs)
        $categories = Category::where('is_active', true)->get();
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => route('storefront.products', ['category' => $category->slug]),
                'lastmod' => ($category->updated_at ?? now())->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // 3. Sản phẩm chi tiết (Product URLs)
        $products = Product::where('is_active', true)
            ->select(['id', 'slug', 'updated_at'])
            ->latest('id')
            ->limit(2000)
            ->get();

        foreach ($products as $product) {
            $urls[] = [
                'loc' => route('storefront.product-detail', ['slug' => $product->slug]),
                'lastmod' => ($product->updated_at ?? now())->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        $xml = view('storefront.sitemap', ['urls' => $urls])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}
