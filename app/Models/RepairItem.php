<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_ticket_id',
        'product_id',
        'item_name',
        'unit',
        'quantity',
        'cost_price',
        'unit_price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
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
            if ($item->repairTicket) {
                $item->repairTicket->recalculateTotals();
            }
        });

        static::created(function ($item) {
            if ($item->product_id && $item->product) {
                $item->product->deductStock($item->quantity);
            }
        });

        static::deleted(function ($item) {
            if ($item->product_id && $item->product) {
                $item->product->addStock($item->quantity);
            }
            if ($item->repairTicket) {
                $item->repairTicket->recalculateTotals();
            }
        });
    }

    public function repairTicket(): BelongsTo
    {
        return $this->belongsTo(RepairTicket::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
