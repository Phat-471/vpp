<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'placeholder_image',
        'description',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('storefront_active_categories');
            \Illuminate\Support\Facades\Cache::forget('pos_active_categories');
            \Illuminate\Support\Facades\Cache::forget('storefront_category_sections');
        });
        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('storefront_active_categories');
            \Illuminate\Support\Facades\Cache::forget('pos_active_categories');
            \Illuminate\Support\Facades\Cache::forget('storefront_category_sections');
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
