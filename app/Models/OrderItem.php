<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_unit_id',
        'product_name',
        'unit_name',
        'quantity',
        'conversion_rate',
        'cost_price',
        'unit_price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'conversion_rate' => 'integer',
            'cost_price' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function ($item) {
            $item->subtotal = $item->quantity * $item->unit_price;
        });

        static::saved(function ($item) {
            if ($item->order) {
                $item->order->recalculateTotals();
            }
        });

        static::created(function ($item) {
            if ($item->product_id && $item->product) {
                $baseQty = $item->quantity * ($item->conversion_rate ?: 1);
                $item->product->deductStock($baseQty);
            }
        });

        static::deleted(function ($item) {
            if ($item->product_id && $item->product) {
                $baseQty = $item->quantity * ($item->conversion_rate ?: 1);
                $item->product->addStock($baseQty);
            }
            if ($item->order) {
                $item->order->recalculateTotals();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productUnit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class);
    }
}
