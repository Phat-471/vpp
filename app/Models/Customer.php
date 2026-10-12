<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'type',
        'phone',
        'phone_last4',
        'email',
        'password',
        'address',
        'tax_id',
        'debt_balance',
        'credit_limit',
        'notes',
        'zalo_id',
        'zalo_name',
        'zalo_avatar',
        'phone_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'debt_balance' => 'decimal:2',
            'credit_limit' => 'decimal:2',
            'phone_verified_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function ($customer) {
            if ($customer->phone) {
                $cleaned = preg_replace('/\D/', '', $customer->phone);
                $customer->phone_last4 = substr($cleaned, -4);
            }
        });
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function repairTickets(): HasMany
    {
        return $this->hasMany(RepairTicket::class);
    }

    public function zaloVerifications(): HasMany
    {
        return $this->hasMany(ZaloVerification::class);
    }

    public function cashTransactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function isPhoneVerified(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    public function isActivated(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    public function isZaloLinked(): bool
    {
        return !empty($this->zalo_id);
    }
}
