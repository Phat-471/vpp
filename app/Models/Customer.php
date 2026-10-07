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
        'phone',
        'phone_last4',
        'email',
        'password',
        'address',
        'debt_balance',
        'notes',
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
}
