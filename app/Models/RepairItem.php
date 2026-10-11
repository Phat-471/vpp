<?php

namespace App\Models;

use App\Models\Product;
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
            $ticket = $item->repairTicket()->first() ?? $item->repairTicket;
            if ($ticket) {
                $ticket->recalculateTotals();
            }
        });

        static::created(function ($item) {
            if ($item->product_id && $item->product) {
                $ticket = $item->repairTicket()->first() ?? $item->repairTicket;
                if (!$ticket || $ticket->status !== 'cancelled') {
                    $item->product->deductStock($item->quantity);
                }
            }
        });

        static::updating(function ($item) {
            $ticket = $item->repairTicket()->first() ?? $item->repairTicket;
            if ($ticket && $ticket->status === 'cancelled') {
                return;
            }

            if ($item->isDirty('product_id')) {
                $oldProductId = $item->getOriginal('product_id');
                $oldQty = (int) $item->getOriginal('quantity');
                if ($oldProductId && $oldQty > 0) {
                    $oldProduct = Product::find($oldProductId);
                    if ($oldProduct) {
                        $oldProduct->addStock($oldQty);
                    }
                }
                if ($item->product_id && $item->quantity > 0) {
                    $newProduct = Product::find($item->product_id);
                    if ($newProduct) {
                        $newProduct->deductStock($item->quantity);
                    }
                }
            } elseif ($item->isDirty('quantity')) {
                $oldQty = (int) $item->getOriginal('quantity');
                $newQty = (int) $item->quantity;
                $diff = $newQty - $oldQty;
                if ($diff !== 0 && $item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        if ($diff > 0) {
                            $product->deductStock($diff);
                        } else {
                            $product->addStock(abs($diff));
                        }
                    }
                }
            }
        });

        static::deleted(function ($item) {
            if ($item->product_id && $item->product) {
                $ticket = $item->repairTicket()->first();
                if (!$ticket || $ticket->status !== 'cancelled') {
                    $item->product->addStock($item->quantity);
                }
            }
            $ticket = $item->repairTicket()->first() ?? $item->repairTicket;
            if ($ticket) {
                $ticket->recalculateTotals();
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
