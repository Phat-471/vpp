<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrinterModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'model_name',
        'printer_type',
        'compatible_cartridges',
        'notes',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => \Illuminate\Support\Facades\Cache::forget('storefront_printer_models'));
        static::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('storefront_printer_models'));
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_printer')
            ->withPivot('notes')
            ->withTimestamps();
    }

    public function repairTickets(): HasMany
    {
        return $this->hasMany(RepairTicket::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->brand} {$this->model_name}";
    }
}
