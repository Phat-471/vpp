<?php

namespace App\Models;

use App\Enums\PosShiftStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosShift extends Model
{
    protected $fillable = ['uuid', 'cashier_id', 'active_cashier_id', 'status', 'opened_at', 'closed_at', 'opening_cash', 'cash_sales', 'transfer_sales', 'total_sales', 'expected_cash', 'counted_cash', 'difference', 'order_count', 'pending_transfer_count', 'notes'];

    protected function casts(): array
    {
        return [
            'status' => PosShiftStatus::class, 'opened_at' => 'datetime', 'closed_at' => 'datetime',
            'opening_cash' => 'decimal:2', 'cash_sales' => 'decimal:2', 'transfer_sales' => 'decimal:2',
            'total_sales' => 'decimal:2', 'expected_cash' => 'decimal:2', 'counted_cash' => 'decimal:2', 'difference' => 'decimal:2',
        ];
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
