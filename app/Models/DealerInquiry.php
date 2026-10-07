<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'contact_name',
        'phone',
        'email',
        'address',
        'business_type',
        'interested_categories',
        'estimated_monthly_budget',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'interested_categories' => 'array',
        ];
    }
}
