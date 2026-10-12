<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'category',
        'amount',
        'payment_method',
        'customer_id',
        'supplier_id',
        'order_id',
        'repair_ticket_id',
        'purchase_order_id',
        'created_by',
        'status',
        'performed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'performed_at' => 'datetime',
        ];
    }

    public static function generateNextCode(string $type = 'receipt'): string
    {
        $prefix = ($type === 'receipt') ? 'PT' : 'PC';
        $year = date('y');
        $codePrefix = "{$prefix}{$year}";

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

    public static function getCategoryOptions(?string $type = null): array
    {
        $receiptCategories = [
            'customer_debt' => 'Thu nợ khách hàng',
            'retail_sale' => 'Thu bán lẻ POS',
            'repair_service' => 'Thu dịch vụ sửa máy in',
            'other_receipt' => 'Thu khác / Thanh lý tài sản',
        ];

        $paymentCategories = [
            'supplier_debt' => 'Chi trả nợ nhà cung cấp',
            'rent' => 'Chi thuê mặt bằng',
            'utilities' => 'Chi điện, nước, internet',
            'salary' => 'Chi lương nhân viên',
            'operating_expense' => 'Chi phí vận hành / Vật tư',
            'other_payment' => 'Chi khác',
        ];

        if ($type === 'receipt') {
            return $receiptCategories;
        }

        if ($type === 'payment') {
            return $paymentCategories;
        }

        return array_merge($receiptCategories, $paymentCategories);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function repairTicket(): BelongsTo
    {
        return $this->belongsTo(RepairTicket::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isReceipt(): bool
    {
        return $this->type === 'receipt';
    }

    public function isPayment(): bool
    {
        return $this->type === 'payment';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function scopeReceipts(Builder $query): Builder
    {
        return $query->where('type', 'receipt');
    }

    public function scopePayments(Builder $query): Builder
    {
        return $query->where('type', 'payment');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }
}
