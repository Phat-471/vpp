<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhoneOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'phone',
        'otp_code',
        'channel',
        'action',
        'attempts',
        'is_used',
        'expires_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'is_used' => 'boolean',
            'attempts' => 'integer',
            'expires_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return !$this->is_used && !$this->isExpired() && $this->attempts < 5;
    }
}
