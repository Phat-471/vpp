<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Customer;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OtpController extends Controller
{
    public function __construct(
        protected OtpService $otpService
    ) {}

    /**
     * Màn hình nhập mã xác thực OTP 6 số
     */
    public function showVerifyPage(Request $request): View|RedirectResponse
    {
        $pendingRegistration = $request->session()->get('pending_customer_registration');
        $rawPhone = $pendingRegistration['phone'] ?? ($request->query('phone') ?: session('otp_phone'));
        $cleanedPhone = AppHelper::cleanPhone((string) $rawPhone);

        if (empty($cleanedPhone)) {
            return redirect()->route('customer.login')->with('error', 'Vui lòng nhập số điện thoại để tiếp tục xác thực.');
        }

        $customer = Customer::where('phone', $cleanedPhone)->first();
        if ($customer && $customer->isActivated() && auth('customer')->check()) {
            return redirect()->route('customer.profile');
        }

        return view('storefront.auth.otp-verify', [
            'phone' => $cleanedPhone,
            'maskedPhone' => AppHelper::maskPhone($cleanedPhone),
            'customer' => $customer,
            'action' => !empty($pendingRegistration) && $pendingRegistration['phone'] === $cleanedPhone ? 'register' : 'verify',
        ]);
    }

    /**
     * API Gửi mã OTP (hoặc Gửi lại mã khi hết 60s)
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^(0[35789])[0-9]{8}$/'],
            'channel' => 'nullable|in:zns,sms',
            'action' => 'nullable|in:verify,register',
        ]);

        $channel = $request->input('channel', 'zns');
        $action = $request->input('action', 'verify');
        $phone = $request->input('phone');
        $pendingRegistration = $request->session()->get('pending_customer_registration');
        $expectedAction = ($pendingRegistration['phone'] ?? null) === AppHelper::cleanPhone($phone) ? 'register' : 'verify';

        if ($action !== $expectedAction) {
            return response()->json(['success' => false, 'message' => 'Phiên xác thực không hợp lệ. Vui lòng tải lại trang.'], 403);
        }

        $customer = Customer::where('phone', AppHelper::cleanPhone($phone))->first();

        $result = $this->otpService->generate(
            phone: $phone,
            action: $action,
            customer: $customer,
            ip: $request->ip(),
            channel: $channel
        );

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * API Kiểm tra mã OTP & Đăng nhập tài khoản
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^(0[35789])[0-9]{8}$/'],
            'otp' => 'required|digits:6',
            'action' => 'nullable|in:verify,register',
        ], [
            'phone.required' => 'Thiếu số điện thoại xác thực.',
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'otp.size' => 'Mã OTP phải gồm đúng 6 chữ số.',
        ]);

        $phone = $request->input('phone');
        $otp = $request->input('otp');
        $action = $request->input('action', 'verify');
        $pendingRegistration = $request->session()->get('pending_customer_registration');
        $expectedAction = ($pendingRegistration['phone'] ?? null) === AppHelper::cleanPhone($phone) ? 'register' : 'verify';

        if ($action !== $expectedAction) {
            return response()->json(['success' => false, 'message' => 'Phiên xác thực không hợp lệ. Vui lòng tải lại trang.'], 403);
        }

        $result = $this->otpService->verify($phone, $otp, $action);

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        if ($action === 'register') {
            $customer = DB::transaction(function () use ($pendingRegistration, $phone) {
                $cleanedPhone = AppHelper::cleanPhone($phone);
                $customer = Customer::query()->where('phone', $cleanedPhone)->lockForUpdate()->first();

                if ($customer && !empty($customer->password)) {
                    return null;
                }

                $attributes = [
                    'name' => $pendingRegistration['name'],
                    'email' => $pendingRegistration['email'] ?: $customer?->email,
                    'address' => $pendingRegistration['address'] ?: $customer?->address,
                    'password' => $pendingRegistration['password_hash'],
                    'phone_verified_at' => now(),
                ];

                if ($customer) {
                    $customer->update($attributes);
                    return $customer->refresh();
                }

                return Customer::create($attributes + [
                    'phone' => $cleanedPhone,
                    'phone_last4' => substr($cleanedPhone, -4),
                ]);
            });

            if (!$customer) {
                return response()->json(['success' => false, 'message' => 'Tài khoản đã tồn tại. Vui lòng đăng nhập.'], 409);
            }

            $request->session()->forget(['pending_customer_registration', 'otp_phone']);
            auth('customer')->login($customer, true);
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công. Đang chuyển hướng…',
                'redirect_url' => route('customer.profile'),
                'customer' => ['name' => $customer->name, 'phone' => $customer->phone],
            ]);
        }

        // Tự động đăng nhập sau khi xác thực thành công
        if (!empty($result['customer'])) {
            auth('customer')->login($result['customer'], true);
            $request->session()->regenerate();
        }

        return response()->json([
            'success' => true,
            'message' => 'Xác thực tài khoản thành công! Đang chuyển hướng...',
            'redirect_url' => route('customer.profile'),
            'customer' => [
                'name' => $result['customer']?->name,
                'phone' => $result['customer']?->phone,
            ],
        ]);
    }
}
