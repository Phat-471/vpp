<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PosPayment;
use Illuminate\Support\Facades\Gate;

class PosReceiptController extends Controller
{
    public function __invoke(Order $order, PosPayment $payment)
    {
        Gate::authorize('view-pos-order', $order);
        $order->load(['orderItems', 'customer', 'creator']);
        $vietQrUrl = $payment->qrUrl($order);

        return view('print.order', compact('order', 'vietQrUrl'));
    }
}
