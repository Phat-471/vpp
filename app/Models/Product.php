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
        'description',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'retail_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'low_stock_threshold' => 'integer',
            'has_custom_image' => 'boolean',
            'is_service_part' => 'boolean',
            'is_active' => 'boolean',
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
                return asset($this->image_path);
            }
            return asset('storage/' . $this->image_path);
        }

        if ($this->category && $this->category->placeholder_image) {
            return asset($this->category->placeholder_image);
        }

        return asset('images/placeholders/default-product.svg');
    }
}
