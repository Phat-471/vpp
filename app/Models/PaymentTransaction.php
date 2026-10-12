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
        return \Illuminate\Support\Facades\DB::transaction(function () {
            $code = strtoupper(trim($this->reference_code));

            $ticket = RepairTicket::where('ticket_code', $code)->lockForUpdate()->first();
            if ($ticket) {
                if ($ticket->status === 'cancelled') {
                    $this->status = 'unmatched';
                    $this->save();
                    return false;
                }
                $this->repair_ticket_id = $ticket->id;
                $ticket->increment('paid_amount', $this->amount);
                $ticket->refresh();
                $ticket->recalculateTotals();

                $this->status = 'processed';
                $this->save();
                return true;
            }

            $order = Order::where('order_code', $code)->lockForUpdate()->first();
            if ($order) {
                if ($order->status === 'cancelled') {
                    $this->status = 'unmatched';
                    $this->save();
                    return false;
                }
                $this->order_id = $order->id;
                $order->increment('paid_amount', $this->amount);
                $order->refresh();
                $order->recalculateTotals();

                // If fully paid and order was pending, advance to processing
                if ($order->payment_status === 'paid' && $order->status === 'pending') {
                    $order->status = 'processing';
                    $order->save();
                }

                $this->status = 'processed';
                $this->save();
                return true;
            }

            $this->status = 'unmatched';
            $this->save();
            return false;
        });
    }
}
