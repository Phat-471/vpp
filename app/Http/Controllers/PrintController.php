<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Order;
use App\Models\RepairTicket;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function repairTicket($id)
    {
        $ticket = RepairTicket::with(['customer', 'printerModel', 'repairItems', 'technician'])->findOrFail($id);

        $lookupUrl = AppHelper::generateLookupUrl($ticket->ticket_code, $ticket->phone_last4);
        $vietQrUrl = AppHelper::generateVietQrUrl($ticket->remaining_amount ?: $ticket->grand_total, $ticket->ticket_code);

        return view('print.repair-ticket', compact('ticket', 'lookupUrl', 'vietQrUrl'));
    }

    public function repairTicketSticker($id)
    {
        $ticket = RepairTicket::with(['customer', 'printerModel', 'technician'])->findOrFail($id);
        $lookupUrl = AppHelper::generateLookupUrl($ticket->ticket_code, $ticket->phone_last4);

        return view('print.repair-ticket-sticker', compact('ticket', 'lookupUrl'));
    }

    public function order($id)
    {
        $order = Order::with(['orderItems', 'customer', 'creator'])->findOrFail($id);
        $due = max(0, (float) $order->grand_total - (float) $order->paid_amount);
        $vietQrUrl = AppHelper::generateVietQrUrl($due > 0 ? $due : (float) $order->grand_total, $order->order_code);

        return view('print.order', compact('order', 'vietQrUrl'));
    }

    public function vatInvoice($id)
    {
        $order = Order::with(['orderItems', 'customer', 'creator'])->findOrFail($id);
        $vietQrUrl = AppHelper::generateVietQrUrl($order->grand_total - $order->paid_amount ?: $order->grand_total, $order->order_code);

        return view('print.vat-invoice', compact('order', 'vietQrUrl'));
    }
}
