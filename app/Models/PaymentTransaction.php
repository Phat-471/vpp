<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
        'transaction_id',
        'reference_code',
        'order_id',
        'repair_ticket_id',
        'amount',
        'account_number',
        'bank_brand_name',
        'description',
        'transaction_time',
        'raw_payload',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_time' => 'datetime',
            'raw_payload' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function repairTicket(): BelongsTo
    {
        return $this->belongsTo(RepairTicket::class);
    }

    /**
     * Process incoming bank transaction and match with Order or RepairTicket
     */
    public function matchAndApply(): bool
    {
        $code = strtoupper(trim($this->reference_code));

        // 1. Try matching Repair Ticket
        $ticket = RepairTicket::where('ticket_code', $code)->first();
        if ($ticket) {
            $this->repair_ticket_id = $ticket->id;
            $ticket->paid_amount += $this->amount;
            $ticket->recalculateTotals();
            $this->status = 'processed';
            $this->save();
            return true;
        }

        // 2. Try matching Order
        $order = Order::where('order_code', $code)->first();
        if ($order) {
            $this->order_id = $order->id;
            $order->paid_amount += $this->amount;
            $order->recalculateTotals();
            $this->status = 'processed';
            $this->save();
            return true;
        }

        $this->status = 'unmatched';
        $this->save();
        return false;
    }
}
