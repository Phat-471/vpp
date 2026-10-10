@extends('layouts.storefront')

@section('title', 'Đăng Nhập Khách Hàng | ' . ($storefrontSettings['site_name'] ?? 'VPP & Dịch Vụ Máy In'))
@section('meta_description', 'Đăng nhập để theo dõi đơn hàng và tra cứu lịch sử sửa máy in của bạn tại ' . ($storefrontSettings['site_name'] ?? 'VPP & Dịch Vụ Máy In'))

@section('content')
<div class="max-w-md mx-auto px-4 py-8 sm:py-16 space-y-6">

    <!-- Header Box -->
    <div class="text-center space-y-2">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-700 via-indigo-700 to-emerald-600 text-white flex items-center justify-center font-black text-2xl mx-auto shadow-md">
            🔐
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Đăng Nhập Khách Hàng</h1>
        <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">
            Nhập số điện thoại và mật khẩu để quản lý đơn hàng và theo dõi tiến độ sửa chữa máy in.
        </p>
    </div>

    <!-- Main Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-5">

        <!-- Hiển thị thông báo flash -->
        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center space-x-2">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center space-x-2">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold space-y-1">
                <div class="font-bold flex items-center space-x-1.5 text-rose-800">
                    <span>⚠️</span>
                    <span>Đăng nhập không thành công:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-[11px] pl-1 font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Đăng Nhập -->
        <form action="{{ route('customer.post-login') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Số điện thoại -->
            <div>
                <label for="loginPhone" class="block text-xs font-bold text-slate-700 mb-1">
                    Số điện thoại <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        🇻🇳 +84
                    </span>
                    <input type="tel" id="loginPhone" name="phone" required maxlength="10"
                        value="{{ old('phone') }}" placeholder="0901234567"
                        class="w-full text-xs sm:text-sm pl-16 pr-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-mono font-medium transition" />
                </div>
            </div>

            <!-- Mật khẩu -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="loginPassword" class="block text-xs font-bold text-slate-700">
                        Mật khẩu <span class="text-rose-500">*</span>
                    </label>
                    <a href="{{ route('customer.forgot-password') }}" class="text-[11px] font-bold text-indigo-700 hover:underline">
                        Quên mật khẩu?
                    </a>
                </div>
                <input type="password" id="loginPassword" name="password" required placeholder="Nhập mật khẩu của bạn..."
                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-medium transition" />
            </div>

            <!-- Ghi nhớ đăng nhập -->
            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center space-x-2 text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" value="1" checked class="w-4 h-4 accent-indigo-600 rounded" />
                    <span class="font-medium">Duy trì đăng nhập (Ghi nhớ tài khoản trên thiết bị này)</span>
                </label>
            </div>

            <!-- Nút submit -->
            <button type="submit" id="btnLoginSubmit"
                class="w-full py-3.5 px-4 bg-gradient-to-r from-blue-700 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 text-white font-black text-xs sm:text-sm rounded-xl shadow-md shadow-indigo-500/20 transition flex items-center justify-center space-x-2 active:scale-98">
                <span>ĐĂNG NHẬP NGAY</span>
                <span>→</span>
            </button>
        </form>

        <div class="pt-3 border-t border-slate-100 text-center text-xs text-slate-500">
            <span>Chưa có tài khoản?</span>
            <a href="{{ route('customer.register') }}" class="font-bold text-indigo-700 hover:underline ml-1">
                Đăng ký tài khoản mới ngay
            </a>
        </div>

    </div>

    <!-- Tra cứu không cần đăng nhập -->
    <div class="p-4 rounded-2xl bg-blue-50/80 border border-blue-200/60 text-center space-y-1">
        <p class="text-xs font-bold text-blue-900">🔍 Cần tra cứu tiến độ sửa chữa máy in?</p>
        <p class="text-[11px] text-blue-700">
            Bạn không cần đăng nhập. <a href="{{ route('lookup.index') }}" class="font-black underline hover:text-blue-900">Tra cứu nhanh tại đây</a> với Mã phiếu và SĐT.
        </p>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const phoneInput = document.getElementById('loginPhone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 10);
            });
        }
    });
</script>
@endsection