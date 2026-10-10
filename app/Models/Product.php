<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sku',
        'barcode',
        'name',
        'slug',
        'cost_price',
        'retail_price',
        'stock_quantity',
        'low_stock_threshold',
        'base_unit',
        'image_path',
        'has_custom_image',
        'is_service_part',
        'is_active',
        'is_flash_sale',
        'flash_sale_price',
        'is_best_seller',
        'is_featured',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'retail_price' => 'decimal:2',
            'flash_sale_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'low_stock_threshold' => 'integer',
            'has_custom_image' => 'boolean',
            'is_service_part' => 'boolean',
            'is_active' => 'boolean',
            'is_flash_sale' => 'boolean',
            'is_best_seller' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function compatiblePrinters(): BelongsToMany
    {
        return $this->belongsToMany(PrinterModel::class, 'product_printer')
            ->withPivot('notes')
            ->withTimestamps();
    }

    public function repairItems(): HasMany
    {
        return $this->hasMany(RepairItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function booted(): void
    {
        static::saving(function ($product) {
            if (!empty($product->image_path)) {
                $product->has_custom_image = true;

                // Auto optimize and convert to WebP if newly uploaded non-webp file
                $fullPath = storage_path('app/public/' . $product->image_path);
                if (file_exists($fullPath) && !str_ends_with(strtolower($product->image_path), '.webp')) {
                    $optimizer = new \App\Services\ImageOptimizationService();
                    $webpPath = $optimizer->optimizeImage($fullPath, 'products');
                    if ($webpPath) {
                        $product->image_path = $webpPath;
                    }
                }
            } else {
                $product->has_custom_image = false;
            }
        });
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function deductStock(int $quantity): bool
    {
        $this->decrement('stock_quantity', $quantity);
        return true;
    }

    public function addStock(int $quantity): bool
    {
        $this->increment('stock_quantity', $quantity);
        return true;
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->has_custom_image && !empty($this->image_path)) {
            if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://') || str_starts_with($this->image_path, 'images/')) {
                if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://') || file_exists(public_path($this->image_path))) {
                    return str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')
                        ? $this->image_path
                        : asset($this->image_path);
                }
            } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->image_path)) {
                return asset('storage/' . $this->image_path);
            }
        }

        // Smart keyword matcher matching realistic product packshots
        $str = mb_strtolower(($this->name ?? '') . ' ' . ($this->sku ?? ''), 'UTF-8');
        $matchedProductImage = match (true) {
            str_contains($str, 'double a') || str_contains($str, 'dba') => 'images/products/giay-double-a-a4.jpg',
            str_contains($str, 'paperone') || str_contains($str, 'ppo') => 'images/products/giay-paperone-a4.jpg',
            str_contains($str, 'brother') || str_contains($str, 'tn-') || str_contains($str, 'tn2385') => 'images/products/muc-brother-tn2385.jpg',
            str_contains($str, 'cartridge') || str_contains($str, '12a') || str_contains($str, '303') || str_contains($str, 'canon 2900') || str_contains($str, '35a') || str_contains($str, '49a') || str_contains($str, '85a') || str_contains($str, 'hộp mực') || str_contains($str, 'mực in') || str_contains($str, 'drum') || str_contains($str, 'trống in') || str_contains($str, 'gạt mực') || str_contains($str, 'epson') || str_contains($str, 'mực') => 'images/products/muc-cartridge-12a.jpg',
            str_contains($str, 'thiên long') || str_contains($str, 'bút') || str_contains($str, 'tl-0') || str_contains($str, 'pilot') || str_contains($str, 'ruột bút') || str_contains($str, 'gôm') || str_contains($str, 'tẩy') => 'images/products/but-thien-long.jpg',
            str_contains($str, 'bìa') || str_contains($str, 'kokuyo') || str_contains($str, 'kingjim') || str_contains($str, 'my clear') || str_contains($str, 'hồ sơ') || str_contains($str, 'kẹp acco') || str_contains($str, 'file') => 'images/products/bia-cong-kokuyo.jpg',
            str_contains($str, 'bấm kim') || str_contains($str, 'kim bấm') || str_contains($str, 'kẹp bướm') || str_contains($str, 'băng keo') || str_contains($str, 'dao rọc') || str_contains($str, 'kéo') || str_contains($str, 'hồ dán') || str_contains($str, 'keo khô') || str_contains($str, 'deli') => 'images/products/bam-kim-plus.jpg',
            str_contains($str, 'note') || str_contains($str, 'post-it') || str_contains($str, 'sổ') || str_contains($str, 'tập') || str_contains($str, 'tiến phát') || str_contains($str, 'hồng hà') => 'images/products/giay-note-postit.jpg',
            str_contains($str, 'giấy') || str_contains($str, 'bãi bằng') || str_contains($str, 'ik plus') || str_contains($str, 'supreme') || str_contains($str, 'decal') || str_contains($str, 'bill') => 'images/products/giay-double-a-a4.jpg',
            default => null,
        };

        if ($matchedProductImage && file_exists(public_path($matchedProductImage))) {
            return asset($matchedProductImage);
        }

        $categorySlug = $this->category?->slug ?? '';
        $categoryPhoto = match (true) {
            str_contains($categorySlug, 'giay') => 'paper-photo.webp',
            str_contains($categorySlug, 'but') => 'writing-photo.webp',
            str_contains($categorySlug, 'bia') => 'folders-photo.webp',
            str_contains($categorySlug, 'so'), str_contains($categorySlug, 'tap'), str_contains($categorySlug, 'note') => 'notebooks-photo.webp',
            str_contains($categorySlug, 'hop-muc'), str_contains($categorySlug, 'muc') => 'printer-photo.webp',
            str_contains($categorySlug, 'may-in'), str_contains($categorySlug, 'linh-kien') => 'printer-photo.webp',
            default => 'folders-photo.webp',
        };

        if ($this->category?->placeholder_image && !str_starts_with($this->category->placeholder_image, 'images/placeholders/')) {
            return asset($this->category->placeholder_image);
        }

        return asset('images/store/' . $categoryPhoto);
    }
}
