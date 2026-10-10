@extends('layouts.storefront')

@section('title', 'Quên Mật Khẩu & Đặt Lại Mật Khẩu | ' . ($storefrontSettings['site_name'] ?? 'VPP & Dịch Vụ Máy In'))
@section('meta_description', 'Đặt lại mật khẩu tài khoản khách hàng nhanh chóng và bảo mật tại ' . ($storefrontSettings['site_name'] ?? 'VPP & Dịch Vụ Máy In'))

@section('content')
<div class="max-w-md mx-auto px-4 py-8 sm:py-16 space-y-6">

    <!-- Header Box -->
    <div class="text-center space-y-2">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-500 via-indigo-700 to-blue-700 text-white flex items-center justify-center font-black text-2xl mx-auto shadow-md">
            🔑
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Đặt Lại Mật Khẩu</h1>
        <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">
            Nhập số điện thoại đã đăng ký và tạo mật khẩu mới để tiếp tục đăng nhập quản lý đơn hàng.
        </p>
    </div>

    <!-- Main Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-5">

        <!-- Thông báo Flash -->
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
                    <span>Vui lòng kiểm tra lại thông tin:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-[11px] pl-1 font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Đặt Lại Mật Khẩu -->
        <form action="{{ route('customer.post-forgot-password') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Honeypot chống bot -->
            <div style="display:none !important;" aria-hidden="true">
                <input type="text" name="website_url" id="fp-website-url" tabindex="-1" autocomplete="off" />
            </div>

            <!-- Số điện thoại -->
            <div>
                <label for="forgotPhone" class="block text-xs font-bold text-slate-700 mb-1">
                    Số điện thoại đã đăng ký <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        🇻🇳 +84
                    </span>
                    <input type="tel" id="forgotPhone" name="phone" required maxlength="10"
                        value="{{ old('phone') }}" placeholder="0901234567"
                        class="w-full text-xs sm:text-sm pl-16 pr-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-mono font-medium transition" />
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block">Nhập đủ 10 số di động của bạn</span>
            </div>

            <!-- Mật khẩu mới -->
            <div>
                <label for="newPassword" class="block text-xs font-bold text-slate-700 mb-1">
                    Mật khẩu mới <span class="text-rose-500">*</span>
                </label>
                <input type="password" id="newPassword" name="new_password" required minlength="6"
                    placeholder="Tối thiểu 6 ký tự..."
                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-medium transition" />
            </div>

            <!-- Xác nhận mật khẩu mới -->
            <div>
                <label for="newPasswordConfirm" class="block text-xs font-bold text-slate-700 mb-1">
                    Xác nhận mật khẩu mới <span class="text-rose-500">*</span>
                </label>
                <input type="password" id="newPasswordConfirm" name="new_password_confirmation" required minlength="6"
                    placeholder="Nhập lại mật khẩu mới..."
                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-medium transition" />
            </div>

            <!-- Nút Submit -->
            <button type="submit" id="btnForgotSubmit"
                class="w-full py-3.5 px-4 bg-gradient-to-r from-blue-700 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 text-white font-black text-xs sm:text-sm rounded-xl shadow-md shadow-indigo-500/20 transition flex items-center justify-center space-x-2 active:scale-98">
                <span>LƯU MẬT KHẨU MỚI & ĐĂNG NHẬP</span>
                <span>→</span>
            </button>
        </form>

        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <a href="{{ route('customer.login') }}" class="font-bold text-indigo-700 hover:underline">
                ← Quay lại Đăng nhập
            </a>
            <a href="{{ route('customer.register') }}" class="text-slate-500 hover:text-indigo-700">
                Đăng ký tài khoản mới
            </a>
        </div>

    </div>

    <!-- Hỗ Trợ Trực Tiếp Nhanh Qua Zalo / Hotline -->
    <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200/80 space-y-2">
        <div class="flex items-center space-x-2 text-amber-900 font-bold text-xs">
            <span>💬</span>
            <span>Cần hỗ trợ đặt lại mật khẩu khẩn cấp?</span>
        </div>
        <p class="text-[11px] text-amber-800 leading-relaxed">
            Nếu bạn không nhớ số điện thoại hoặc cần nhân viên kỹ thuật hỗ trợ trực tiếp, vui lòng liên hệ hotline hoặc Zalo để được cấp lại mật khẩu ngay trong 30 giây:
        </p>
        <div class="flex flex-wrap gap-2 pt-1">
            <a href="{{ $storefrontSettings['zalo_url'] ?? 'https://zalo.me/0974194305' }}" target="_blank"
                class="px-3 py-1.5 bg-[#0068ff] hover:bg-blue-600 text-white rounded-lg text-xs font-bold transition flex items-center space-x-1">
                <span>Zalo Hỗ Trợ 24/7</span>
            </a>
            <a href="{{ $storefrontSettings['hotline_url'] ?? 'tel:0974194305' }}"
                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center space-x-1">
                <span>📞 Hotline: {{ $storefrontSettings['hotline'] ?? '0974.194.305' }}</span>
            </a>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const phoneInput = document.getElementById('forgotPhone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 10);
            });
        }
    });
</script>
@endsection
