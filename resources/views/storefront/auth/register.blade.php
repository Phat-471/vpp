@extends('layouts.storefront')

@section('title', 'Đăng Ký Tài Khoản Khách Hàng | ' . ($storefrontSettings['site_name'] ?? 'VPP & Dịch Vụ Máy In'))
@section('meta_description', 'Tạo tài khoản mua sắm văn phòng phẩm và theo dõi dịch vụ máy in nhanh chóng chỉ trong 10 giây.')

@section('content')
<div class="max-w-lg mx-auto px-4 py-8 sm:py-12 space-y-6">

    <!-- Header Box -->
    <div class="text-center space-y-2">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-700 via-indigo-700 to-emerald-600 text-white flex items-center justify-center font-black text-2xl mx-auto shadow-md">
            ✍️
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Đăng Ký Tài Khoản</h1>
        <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">
            Đăng ký nhanh chỉ 10 giây để lưu địa chỉ giao hàng, nhận ưu đãi giá sỉ.
        </p>
    </div>

    <!-- Main Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-5">

        <!-- Hiển thị lỗi server nếu có -->
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

        <!-- Form Đăng Ký Chuẩn 2 Lớp Bảo Vệ Chống Spam -->
        <form id="registerForm" action="{{ route('customer.post-register') }}" method="POST" class="space-y-4" novalidate>
            @csrf

            <!-- Honeypot chống bot spam (ẩn hoàn toàn) -->
            <div style="display:none !important;" aria-hidden="true">
                <input type="text" name="website" tabindex="-1" autocomplete="off" />
            </div>

            <!-- Họ và tên -->
            <div>
                <label for="regName" class="block text-xs font-bold text-slate-700 mb-1">
                    Họ và tên của bạn <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="regName" name="name" required minlength="2" maxlength="100"
                    value="{{ old('name') }}" placeholder="VD: Nguyễn Văn An"
                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border @error('name') border-rose-400 bg-rose-50/30 @else border-slate-300 bg-slate-50 @enderror focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-medium transition" />
                @error('name')
                    <p class="mt-1 text-[11px] font-bold text-rose-600">{{ $message }}</p>
                @enderror
                <p id="nameErrorClient" class="mt-1 text-[11px] font-bold text-rose-600 hidden"></p>
            </div>

            <!-- Số điện thoại -->
            <div>
                <label for="regPhone" class="block text-xs font-bold text-slate-700 mb-1">
                    Số điện thoại đăng nhập <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        🇻🇳 +84
                    </span>
                    <input type="tel" id="regPhone" name="phone" required maxlength="10"
                        value="{{ old('phone') }}" placeholder="0901234567"
                        class="w-full text-xs sm:text-sm pl-16 pr-3.5 py-2.5 rounded-xl border @if($errors->has('phone') || $errors->has('cleaned_phone')) border-rose-400 bg-rose-50/30 @else border-slate-300 bg-slate-50 @enderror focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-mono font-medium transition" />
                </div>
                @error('phone')
                    <p class="mt-1 text-[11px] font-bold text-rose-600">{{ $message }}</p>
                @enderror
                @error('cleaned_phone')
                    <p class="mt-1 text-[11px] font-bold text-rose-600">{{ $message }}</p>
                @enderror
                <p id="phoneErrorClient" class="mt-1 text-[11px] font-bold text-rose-600 hidden"></p>
                <p id="phoneHelp" class="mt-1 text-[10px] text-slate-400">Đầu số: 03, 05, 07, 08, 09 (gồm đúng 10 chữ số)</p>
            </div>

            <!-- Mật khẩu -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="regPassword" class="block text-xs font-bold text-slate-700 mb-1">
                        Mật khẩu <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" id="regPassword" name="password" required minlength="6" placeholder="Tối thiểu 6 ký tự"
                        class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border @error('password') border-rose-400 bg-rose-50/30 @else border-slate-300 bg-slate-50 @enderror focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-medium transition" />
                    @error('password')
                        <p class="mt-1 text-[11px] font-bold text-rose-600">{{ $message }}</p>
                    @enderror
                    <p id="passwordLenClient" class="mt-1 text-[11px] font-bold text-rose-600 hidden"></p>
                </div>
                <div>
                    <label for="regPasswordConfirm" class="block text-xs font-bold text-slate-700 mb-1">
                        Xác nhận lại <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" id="regPasswordConfirm" name="password_confirmation" required minlength="6" placeholder="Nhập lại mật khẩu"
                        class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-medium transition" />
                    <p id="passwordMatchClient" class="mt-1 text-[11px] font-bold hidden"></p>
                </div>
            </div>

            <!-- Địa chỉ nhận hàng & Email (Tùy chọn) -->
            <div>
                <label for="regAddress" class="block text-xs font-bold text-slate-700 mb-1">
                    Địa chỉ nhận hàng (tùy chọn)
                </label>
                <input type="text" id="regAddress" name="address" value="{{ old('address') }}" placeholder="Số nhà, tên đường, phường/xã, quận/huyện..."
                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-medium transition" />
            </div>

            <div>
                <label for="regEmail" class="block text-xs font-bold text-slate-700 mb-1">
                    Email nhận hóa đơn VAT (tùy chọn)
                </label>
                <input type="email" id="regEmail" name="email" value="{{ old('email') }}" placeholder="VD: ketoan@congty.com"
                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-medium transition" />
                @error('email')
                    <p class="mt-1 text-[11px] font-bold text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nút bấm Đăng Ký -->
            <button type="submit" id="btnRegisterSubmit"
                class="w-full py-3.5 px-4 bg-gradient-to-r from-blue-700 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 text-white font-black text-xs sm:text-sm rounded-xl shadow-md shadow-indigo-500/20 transition flex items-center justify-center space-x-2 active:scale-98">
                <span>HOÀN TẤT ĐĂNG KÝ TÀI KHOẢN</span>
                <span>→</span>
            </button>
        </form>

        <div class="pt-3 border-t border-slate-100 text-center text-xs text-slate-500">
            <span>Đã có tài khoản?</span>
            <a href="{{ route('customer.login') }}" class="font-bold text-indigo-700 hover:underline ml-1">Đăng nhập tại đây</a>
        </div>

    </div>

    <!-- Cam kết an toàn thông tin -->
    <div class="text-center text-[11px] text-slate-400 space-y-1">
        <p>🔒 Thông tin của Quý khách được mã hóa và bảo mật theo chuẩn an toàn.</p>
        <p>Hotline hỗ trợ: <a href="{{ $storefrontSettings['hotline_url'] ?? 'tel:0974194305' }}" class="font-bold text-slate-600 underline">{{ $storefrontSettings['hotline'] ?? '0974.194.305' }}</a></p>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const nameInput = document.getElementById('regName');
        const phoneInput = document.getElementById('regPhone');
        const pwdInput = document.getElementById('regPassword');
        const pwdConfirmInput = document.getElementById('regPasswordConfirm');
        const form = document.getElementById('registerForm');

        const nameError = document.getElementById('nameErrorClient');
        const phoneError = document.getElementById('phoneErrorClient');
        const pwdError = document.getElementById('passwordLenClient');
        const pwdMatch = document.getElementById('passwordMatchClient');

        // Format điện thoại
        phoneInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
            validatePhone();
        });

        function validatePhone() {
            const val = phoneInput.value.trim();
            const regex = /^(0[35789])[0-9]{8}$/;
            if (!val) {
                phoneError.textContent = 'Vui lòng nhập số điện thoại.';
                phoneError.classList.remove('hidden');
                return false;
            } else if (!regex.test(val)) {
                phoneError.textContent = 'Số điện thoại phải đủ 10 số di động VN (bắt đầu bằng 03, 05, 07, 08, 09).';
                phoneError.classList.remove('hidden');
                return false;
            } else {
                phoneError.classList.add('hidden');
                return true;
            }
        }

        function validatePassword() {
            const val = pwdInput.value;
            if (val.length < 6) {
                pwdError.textContent = 'Mật khẩu phải có từ 6 ký tự trở lên.';
                pwdError.classList.remove('hidden');
                return false;
            } else {
                pwdError.classList.add('hidden');
                return true;
            }
        }

        function validateMatch() {
            if (pwdConfirmInput.value && pwdConfirmInput.value !== pwdInput.value) {
                pwdMatch.textContent = 'Mật khẩu xác nhận không khớp.';
                pwdMatch.className = 'mt-1 text-[11px] font-bold text-rose-600';
                pwdMatch.classList.remove('hidden');
                return false;
            } else if (pwdConfirmInput.value && pwdConfirmInput.value === pwdInput.value) {
                pwdMatch.textContent = '✓ Mật khẩu khớp hoàn toàn.';
                pwdMatch.className = 'mt-1 text-[11px] font-bold text-emerald-600';
                pwdMatch.classList.remove('hidden');
                return true;
            } else {
                pwdMatch.classList.add('hidden');
                return true;
            }
        }

        pwdInput.addEventListener('input', function() {
            validatePassword();
            validateMatch();
        });

        pwdConfirmInput.addEventListener('input', validateMatch);

        form.addEventListener('submit', function(e) {
            let isValid = true;
            if (nameInput.value.trim().length < 2) {
                nameError.textContent = 'Vui lòng nhập họ và tên (ít nhất 2 ký tự).';
                nameError.classList.remove('hidden');
                isValid = false;
            } else {
                nameError.classList.add('hidden');
            }

            if (!validatePhone()) isValid = false;
            if (!validatePassword()) isValid = false;
            if (pwdConfirmInput.value !== pwdInput.value) {
                validateMatch();
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
