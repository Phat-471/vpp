<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Order;
use App\Models\RepairTicket;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    /**
     * Show lookup search form
     */
    public function index()
    {
        return view('lookup.index');
    }

    /**
     * Handle search submission with anti-IDOR validation
     */
    public function search(Request $request)
    {
        $rawCode = $request->input('code') ?: $request->input('ticket_code');

        $request->validate([
            'phone_last4' => 'required|digits:4',
        ], [
            'phone_last4.required' => 'Vui lòng nhập 4 số cuối số điện thoại.',
            'phone_last4.digits' => '4 số cuối số điện thoại phải gồm đúng 4 chữ số.',
        ]);

        if (empty($rawCode)) {
            return back()->withInput()->withErrors([
                'lookup_error' => 'Vui lòng nhập Mã đơn hàng hoặc Mã phiếu sửa chữa.',
            ]);
        }

        $code = strtoupper(trim($rawCode));
        $phone4 = trim($request->phone_last4);

        // 1. Kiểm tra bảng Đơn Hàng (Order)
        $order = Order::where(function ($q) use ($code) {
                $q->where('order_code', $code)
                  ->orWhere('uuid', $code);
            })
            ->where(function ($q) use ($phone4) {
                $q->whereRaw("RIGHT(REGEXP_REPLACE(customer_phone, '[^0-9]', ''), 4) = ?", [$phone4])
                  ->orWhere('customer_phone', 'LIKE', "%{$phone4}");
            })
            ->first();

        if ($order) {
            session()->put("verified_order_{$order->uuid}", true);
            return redirect()->route('lookup.order', ['code' => $order->order_code]);
        }

        // 2. Kiểm tra bảng Phiếu Sửa Chữa (RepairTicket)
        $ticket = RepairTicket::where(function ($q) use ($code) {
                $q->where('ticket_code', $code)
                  ->orWhere('uuid', $code);
            })
            ->where('phone_last4', $phone4)
            ->first();

        if ($ticket) {
            session()->put("verified_ticket_{$ticket->uuid}", true);
            return redirect()->route('lookup.view', ['code' => $ticket->ticket_code]);
        }

        return back()->withInput()->withErrors([
            'lookup_error' => 'Không tìm thấy đơn hàng hoặc phiếu sửa chữa nào khớp với thông tin đã nhập. Quý khách vui lòng kiểm tra lại Mã và 4 số cuối SĐT.',
        ]);
    }

    /**
     * Display ticket progress details
     */
    public function view($code, Request $request)
    {
        $code = strtoupper(trim($code));

        // Tự động chuyển tiếp nếu là mã đơn hàng
        if (str_starts_with($code, 'HD') || Order::where('order_code', $code)->orWhere('uuid', $code)->exists()) {
            return $this->orderView($code, $request);
        }

        $ticket = RepairTicket::with(['repairItems', 'printerModel', 'technician'])
            ->where('ticket_code', $code)
            ->orWhere('uuid', $code)
            ->firstOrFail();

        // Anti-IDOR check: verify either URL param phone4 or active session
        $phone4Param = $request->query('phone4');
        $isSessionVerified = session()->get("verified_ticket_{$ticket->uuid}", false);

        if ($phone4Param === $ticket->phone_last4) {
            session()->put("verified_ticket_{$ticket->uuid}", true);
            $isSessionVerified = true;
        }

        if (!$isSessionVerified) {
            return view('lookup.verify', [
                'type' => 'ticket',
                'ticket' => $ticket,
            ]);
        }

        $vietQrUrl = AppHelper::generateVietQrUrl($ticket->remaining_amount ?: $ticket->grand_total, $ticket->ticket_code);

        return view('lookup.show', compact('ticket', 'vietQrUrl'));
    }

    /**
     * Display order progress details
     */
    public function orderView($code, Request $request)
    {
        $code = strtoupper(trim($code));

        $order = Order::with(['orderItems', 'customer', 'creator'])
            ->where('order_code', $code)
            ->orWhere('uuid', $code)
            ->firstOrFail();

        // Anti-IDOR check: verify either URL param phone4 or active session
        $phone4Param = $request->query('phone4');
        $orderPhone4 = AppHelper::extractPhoneLast4($order->customer_phone);
        $isSessionVerified = session()->get("verified_order_{$order->uuid}", false);

        if ($phone4Param && $phone4Param === $orderPhone4) {
            session()->put("verified_order_{$order->uuid}", true);
            $isSessionVerified = true;
        }

        if (!$isSessionVerified) {
            return view('lookup.verify', [
                'type' => 'order',
                'order' => $order,
            ]);
        }

        $due = max(0, (float) $order->grand_total - (float) $order->paid_amount);
        $vietQrUrl = AppHelper::generateVietQrUrl($due > 0 ? $due : (float) $order->grand_total, $order->order_code);

        return view('lookup.order-show', compact('order', 'vietQrUrl'));
    }
}
