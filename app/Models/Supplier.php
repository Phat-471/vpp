<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'contact_name',
        'phone',
        'email',
        'address',
        'tax_id',
        'debt_balance',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'debt_balance' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public static function generateNextCode(): string
    {
        $last = static::orderByDesc('id')->first();
        $nextId = $last ? ($last->id + 1) : 1;
        return 'NCC' . str_pad((string) $nextId, 3, '0', STR_PAD_LEFT);
    }
}
