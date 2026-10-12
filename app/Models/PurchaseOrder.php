<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'supplier_id',
        'status',
        'total_amount',
        'discount_amount',
        'grand_total',
        'paid_amount',
        'debt_amount',
        'payment_status',
        'payment_method',
        'received_at',
        'created_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'debt_amount' => 'decimal:2',
            'received_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function isReceived(): bool
    {
        return $this->status === 'received';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public static function generateNextCode(): string
    {
        $prefix = 'NK' . date('y');
        $last = static::where('code', 'like', "{$prefix}%")->orderByDesc('id')->first();
        if ($last && preg_match('/' . $prefix . '(\d+)/', $last->code, $matches)) {
            $num = (int) $matches[1] + 1;
        } else {
            $num = 1;
        }
        return $prefix . str_pad((string) $num, 4, '0', STR_PAD_LEFT);
    }
}
