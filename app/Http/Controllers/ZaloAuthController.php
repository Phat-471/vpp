<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\ZaloVerification;
use App\Services\ZaloAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ZaloAuthController extends Controller
{
    public function __construct(
        protected ZaloAuthService $zaloAuthService
    ) {}

    /**
     * Khởi tạo phiên quét QR mới cho trang Đăng ký / Đăng nhập
     */
    public function init(Request $request): JsonResponse
    {
        $verification = $this->zaloAuthService->createSession(
            $request->ip(),
            $request->userAgent()
        );

        $qrData = $this->zaloAuthService->getSessionQrData($verification);

        return response()->json([
            'success' => true,
            'data' => $qrData,
        ]);
    }

    /**
     * Kiểm tra trạng thái phiên (Web Client polling mỗi 2 giây)
     */
    public function check(Request $request, string $token): JsonResponse
    {
        $status = $this->zaloAuthService->checkStatus($token);

        // Nếu đã được xác thực, tự động đăng nhập session trên máy tính
        if (!empty($status['verified']) && !empty($status['customer_id'])) {
            $verification = ZaloVerification::with('customer')->where('token', $token)->first();
            if ($verification && $verification->customer) {
                auth('customer')->login($verification->customer, true);
                $status['redirect_url'] = route('customer.profile');
            }
        }

        return response()->json([
            'success' => true,
            'data' => $status,
        ]);
    }

    /**
     * Màn hình hiển thị mã QR Zalo trên máy tính sau khi khách vừa điền form đăng ký
     */
    public function showVerificationPage(Request $request, string $token): View
    {
        $verification = ZaloVerification::with('customer')->where('token', $token)->firstOrFail();
        $qrData = $this->zaloAuthService->getSessionQrData($verification);

        return view('storefront.auth.zalo-verify-page', [
            'verification' => $verification,
            'qrData' => $qrData,
            'customer' => $verification->customer,
        ]);
    }

    /**
     * Màn hình mở ra trên điện thoại khi khách quét mã QR Zalo
     */
    public function prompt(Request $request, string $token): View
    {
        $verification = ZaloVerification::where('token', $token)->firstOrFail();

        return view('storefront.auth.zalo-prompt', [
            'verification' => $verification,
            'isExpired' => $verification->isExpired(),
            'isVerified' => $verification->isVerified(),
        ]);
    }

    /**
     * Khách hàng bấm "Cho phép & Chia sẻ thông tin" trên điện thoại
     */
    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'zalo_id' => 'nullable|string|max:100',
            'avatar' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Vui lòng cung cấp tên hiển thị Zalo.',
            'phone.required' => 'Vui lòng cung cấp số điện thoại Zalo.',
        ]);

        $result = $this->zaloAuthService->confirmVerification(
            $request->token,
            $request->name,
            $request->phone,
            $request->zalo_id,
            $request->avatar
        );

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Xác thực tài khoản qua Zalo thành công!',
            'data' => [
                'customer_name' => $result['customer']->name,
                'customer_phone' => AppHelper::maskPhone($result['customer']->phone),
            ],
        ]);
    }

    /**
     * Hỗ trợ giả lập/kiểm thử tức thì trên máy tính (Demo Sandbox)
     */
    public function mockConfirm(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        // Tạo thông tin khách mẫu chuẩn
        $sampleNames = ['Nguyễn Văn Khang', 'Trần Thị Thu Thảo', 'Phạm Hoàng Minh', 'Lê Quỳnh Anh'];
        $samplePhones = ['0988765432', '0912345678', '0938889999', '0977665544'];

        $idx = array_rand($sampleNames);
        $name = $request->input('name', $sampleNames[$idx]);
        $phone = $request->input('phone', $samplePhones[$idx]);
        $zaloId = 'zalo_' . substr(md5($phone), 0, 12);

        $result = $this->zaloAuthService->confirmVerification(
            $request->token,
            $name,
            $phone,
            $zaloId
        );

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        // Tự động đăng nhập
        if (!empty($result['customer'])) {
            auth('customer')->login($result['customer'], true);
        }

        return response()->json([
            'success' => true,
            'message' => 'Giả lập khách quét Zalo thành công!',
            'redirect_url' => route('customer.profile'),
            'data' => [
                'customer_name' => $result['customer']->name,
                'customer_phone' => AppHelper::maskPhone($result['customer']->phone),
            ],
        ]);
    }
}
