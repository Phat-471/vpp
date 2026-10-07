<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'order_code',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'channel',
        'status',
        'subtotal',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'grand_total',
        'paid_amount',
        'payment_status',
        'payment_method',
        'is_vat_invoice',
        'company_name',
        'company_tax_id',
        'company_address',
        'invoice_email',
        'shipping_fee',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_vat_invoice' => 'boolean',
            'shipping_fee' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
            if (empty($order->order_code)) {
                $prefix = 'HD' . date('y');
                $latest = static::where('order_code', 'LIKE', "{$prefix}%")->count();
                $order->order_code = $prefix . str_pad($latest + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function recalculateTotals(): void
    {
        $this->subtotal = $this->orderItems()->sum('subtotal');
        $taxable = max(0, $this->subtotal - $this->discount_amount);
        $this->tax_amount = round($taxable * ($this->tax_rate / 100), 2);
        $this->grand_total = $taxable + $this->tax_amount;

        if ($this->paid_amount >= $this->grand_total && $this->grand_total > 0) {
            $this->payment_status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->payment_status = 'partially_paid';
        } else {
            $this->payment_status = 'unpaid';
        }

        $this->save();
    }
}
