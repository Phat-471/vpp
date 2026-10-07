<?php

namespace App\Http\Controllers;

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
        $request->validate([
            'ticket_code' => 'required|string|max:30',
            'phone_last4' => 'required|digits:4',
        ], [
            'ticket_code.required' => 'Vui lòng nhập Mã phiếu sửa chữa.',
            'phone_last4.required' => 'Vui lòng nhập 4 số cuối số điện thoại.',
            'phone_last4.digits' => '4 số cuối số điện thoại phải gồm đúng 4 chữ số.',
        ]);

        $code = strtoupper(trim($request->ticket_code));
        $phone4 = trim($request->phone_last4);

        $ticket = RepairTicket::where(function ($q) use ($code) {
                $q->where('ticket_code', $code)
                  ->orWhere('uuid', $code);
            })
            ->where('phone_last4', $phone4)
            ->first();

        if (!$ticket) {
            return back()->withInput()->withErrors([
                'lookup_error' => 'Không tìm thấy phiếu sửa chữa khớp với Mã phiếu và 4 số cuối SĐT đã nhập. Quý khách vui lòng kiểm tra lại.',
            ]);
        }

        // Save verification in session for anti-IDOR access
        session()->put("verified_ticket_{$ticket->uuid}", true);

        return redirect()->route('lookup.view', ['code' => $ticket->ticket_code]);
    }

    /**
     * Display ticket progress details
     */
    public function view($code, Request $request)
    {
        $code = strtoupper(trim($code));

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
            return view('lookup.verify', compact('ticket'));
        }

        $vietQrUrl = \App\Helpers\AppHelper::generateVietQrUrl($ticket->remaining_amount ?: $ticket->grand_total, $ticket->ticket_code);

        return view('lookup.show', compact('ticket', 'vietQrUrl'));
    }
}
