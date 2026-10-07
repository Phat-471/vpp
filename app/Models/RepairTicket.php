<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class RepairTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'ticket_code',
        'customer_id',
        'customer_name',
        'customer_phone',
        'phone_last4',
        'printer_model_id',
        'device_name',
        'serial_number',
        'accessories',
        'issue_description',
        'technician_diagnosis',
        'intake_flow',
        'status',
        'labor_fee',
        'parts_total',
        'discount_amount',
        'tax_amount',
        'grand_total',
        'paid_amount',
        'payment_status',
        'payment_method',
        'promised_at',
        'completed_at',
        'delivered_at',
        'technician_id',
        'created_by',
        'internal_notes',
    ];

    protected function casts(): array
    {
        return [
            'labor_fee' => 'decimal:2',
            'parts_total' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'promised_at' => 'datetime',
            'completed_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($ticket) {
            if (empty($ticket->uuid)) {
                $ticket->uuid = (string) Str::uuid();
            }
            if (empty($ticket->ticket_code)) {
                $prefix = 'SC' . date('y');
                $latest = static::where('ticket_code', 'LIKE', "{$prefix}%")->count();
                $ticket->ticket_code = $prefix . str_pad($latest + 1, 4, '0', STR_PAD_LEFT);
            }
            if ($ticket->customer_phone && empty($ticket->phone_last4)) {
                $cleaned = preg_replace('/\D/', '', $ticket->customer_phone);
                $ticket->phone_last4 = substr($cleaned, -4);
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function printerModel(): BelongsTo
    {
        return $this->belongsTo(PrinterModel::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function repairItems(): HasMany
    {
        return $this->hasMany(RepairItem::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function recalculateTotals(): void
    {
        $this->parts_total = $this->repairItems()->sum('subtotal');
        $this->grand_total = max(0, ($this->labor_fee + $this->parts_total + $this->tax_amount) - $this->discount_amount);
        
        if ($this->paid_amount >= $this->grand_total && $this->grand_total > 0) {
            $this->payment_status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->payment_status = 'partially_paid';
        } else {
            $this->payment_status = 'unpaid';
        }
        
        $this->save();
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->grand_total - (float) $this->paid_amount);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'received' => 'Đã tiếp nhận',
            'diagnosing' => 'Đang kiểm tra',
            'waiting_approval' => 'Chờ khách duyệt giá',
            'in_progress' => 'Đang sửa chữa',
            'completed' => 'Đã sửa xong',
            'delivered' => 'Đã bàn giao khách',
            'cancelled' => 'Đã hủy',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'received' => 'gray',
            'diagnosing' => 'warning',
            'waiting_approval' => 'info',
            'in_progress' => 'primary',
            'completed' => 'success',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'gray',
        };
    }
}
