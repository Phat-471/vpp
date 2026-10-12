<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'created_by',
        'status',
        'total_items',
        'matched_items',
        'difference_items',
        'total_system_qty',
        'total_actual_qty',
        'total_difference_value',
        'notes',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_difference_value' => 'decimal:2',
            'completed_at' => 'datetime',
        ];
    }

    public static function generateNextCode(): string
    {
        $year = date('y');
        $codePrefix = "KK{$year}";

        $last = static::where('code', 'like', "{$codePrefix}%")
            ->orderByDesc('id')
            ->first();

        if ($last && preg_match("/^{$codePrefix}(\d{4})$/", $last->code, $matches)) {
            $seq = (int) $matches[1] + 1;
        } else {
            $seq = 1;
        }

        return sprintf('%s%04d', $codePrefix, $seq);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockCheckItem::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function recalculateTotals(): void
    {
        $items = $this->items()->get();
        $totalItems = $items->count();
        $matchedItems = $items->where('difference', 0)->count();
        $diffItems = $items->where('difference', '!=', 0)->count();
        $sysQty = (int) $items->sum('system_quantity');
        $actQty = (int) $items->sum('actual_quantity');
        $diffValue = (float) $items->sum('difference_value');

        $this->update([
            'total_items' => $totalItems,
            'matched_items' => $matchedItems,
            'difference_items' => $diffItems,
            'total_system_qty' => $sysQty,
            'total_actual_qty' => $actQty,
            'total_difference_value' => $diffValue,
        ]);
    }
}
