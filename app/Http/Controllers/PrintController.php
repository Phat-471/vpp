<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Order;
use App\Models\PosShift;
use App\Models\RepairTicket;
use Illuminate\Support\Facades\Gate;

class PrintController extends Controller
{
    public function repairTicket($id)
    {
        $ticket = RepairTicket::with(['customer', 'printerModel', 'repairItems', 'technician'])
            ->where('uuid', $id)
            ->orWhere('id', $id)
            ->firstOrFail();
        $this->authorizeTicketPrint($ticket);

        $lookupUrl = AppHelper::generateLookupUrl($ticket->ticket_code, $ticket->phone_last4);
        $vietQrUrl = AppHelper::generateVietQrUrl($ticket->remaining_amount ?: $ticket->grand_total, $ticket->ticket_code);

        return view('print.repair-ticket', compact('ticket', 'lookupUrl', 'vietQrUrl'));
    }

    public function repairTicketSticker($id)
    {
        $ticket = RepairTicket::with(['customer', 'printerModel', 'technician'])
            ->where('uuid', $id)
            ->orWhere('id', $id)
            ->firstOrFail();
        $this->authorizeTicketPrint($ticket);
        $lookupUrl = AppHelper::generateLookupUrl($ticket->ticket_code, $ticket->phone_last4);

        return view('print.repair-ticket-sticker', compact('ticket', 'lookupUrl'));
    }

    public function order($id)
    {
        $order = Order::with(['orderItems', 'customer', 'creator'])
            ->where('uuid', $id)
            ->orWhere('id', $id)
            ->firstOrFail();
        $this->authorizeOrderPrint($order);
        $due = max(0, (float) $order->grand_total - (float) $order->paid_amount);
        $vietQrUrl = AppHelper::generateVietQrUrl($due > 0 ? $due : (float) $order->grand_total, $order->order_code);
        $phone4 = AppHelper::extractPhoneLast4($order->customer_phone);
        $lookupUrl = AppHelper::generateLookupUrl($order->order_code, $phone4);

        return view('print.order', compact('order', 'vietQrUrl', 'lookupUrl'));
    }

    public function vatInvoice($id)
    {
        $order = Order::with(['orderItems', 'customer', 'creator'])
            ->where('uuid', $id)
            ->orWhere('id', $id)
            ->firstOrFail();
        $this->authorizeOrderPrint($order);
        $vietQrUrl = AppHelper::generateVietQrUrl($order->grand_total - $order->paid_amount ?: $order->grand_total, $order->order_code);

        return view('print.vat-invoice', compact('order', 'vietQrUrl'));
    }

    public function posShift($id)
    {
        Gate::authorize('use-pos');

        $shift = PosShift::with(['cashier', 'orders'])->where('id', $id)->orWhere('uuid', $id)->firstOrFail();
        $summary = app(\App\Services\PosShiftService::class)->summary($shift);

        return view('print.pos-shift', compact('shift', 'summary'));
    }

    private function authorizeTicketPrint(RepairTicket $ticket): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        $user = auth()->user();
        if ($user && ($user->isAdmin() || $user->isTechnician() || $user->isCashier())) {
            return;
        }

        abort_unless((bool) session()->get("verified_ticket_{$ticket->uuid}", false), 403);
    }

    private function authorizeOrderPrint(Order $order): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        $user = auth()->user();
        if ($user && $user->isAdmin()) {
            return;
        }

        if ($user && Gate::allows('view-pos-order', $order)) {
            return;
        }

        abort_unless((bool) session()->get("verified_order_{$order->uuid}", false), 403);
    }
}
