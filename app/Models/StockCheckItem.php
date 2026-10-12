<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockCheckItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_check_id',
        'product_id',
        'product_name',
        'unit_name',
        'system_quantity',
        'actual_quantity',
        'difference',
        'cost_price',
        'difference_value',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'difference_value' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function ($item) {
            $item->difference = (int) $item->actual_quantity - (int) $item->system_quantity;
            $item->difference_value = (float) $item->difference * (float) $item->cost_price;
        });

        static::saved(function ($item) {
            $item->stockCheck?->recalculateTotals();
        });

        static::deleted(function ($item) {
            $item->stockCheck?->recalculateTotals();
        });
    }

    public function stockCheck(): BelongsTo
    {
        return $this->belongsTo(StockCheck::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
