@extends('layouts.storefront')

@section('title', 'Xác Thực Tài Khoản Qua Zalo | VPP & Dịch Vụ Máy In')
@section('meta_description', 'Xác thực tài khoản khách hàng chính chủ qua Zalo nhanh chóng, tiện lợi và hoàn toàn miễn phí.')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-8 px-4 sm:px-6">
    <div class="w-full max-w-lg bg-white rounded-3xl border border-slate-200/80 shadow-2xl p-6 sm:p-8 space-y-6">

        <!-- Top Header Brand & Icon -->
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 text-[#0068ff] flex items-center justify-center text-3xl mx-auto shadow-xs font-black">
                💬
            </div>
            <span class="inline-block px-3 py-0.5 rounded-full bg-blue-50 text-[#0068ff] text-[11px] font-black uppercase tracking-wider border border-blue-200/50">
                XÁC THỰC ZALO CHÍNH CHỦ • 0 ĐỒNG
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900">
                Kích Hoạt Tài Khoản Qua Zalo
            </h1>
            <p class="text-xs text-slate-500 leading-relaxed max-w-sm mx-auto">
                Để bảo vệ quyền lợi mua hàng và xác minh số điện thoại chính chủ
                <strong class="font-mono text-slate-900 font-bold text-sm block mt-0.5">{{ $maskedPhone }}</strong>
            </p>
        </div>

        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-medium flex items-start space-x-2">
                <span class="text-emerald-600 font-bold">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-800 font-medium flex items-start space-x-2">
                <span class="text-amber-600 font-bold">⚠️</span>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        <!-- KHỐI HÀNH ĐỘNG XÁC THỰC ZALO 1 CHẠM (0 ĐỒNG TRỌN ĐỜI) -->
        <div class="p-5 rounded-2xl bg-gradient-to-br from-blue-50 via-indigo-50/40 to-slate-50 border-2 border-blue-200 space-y-4">
            
            <div class="flex items-center justify-between">
                <div class="space-y-0.5">
                    <span class="text-[11px] font-extrabold uppercase tracking-wide text-blue-900 block">
                        MÃ XÁC THỰC CỦA BẠN:
                    </span>
                    <span class="text-[11px] text-slate-500">Dùng mã này để xác thực qua Zalo của Shop</span>
                </div>
                
                <div class="flex items-center space-x-2">
                    <span id="display-otp-code" class="text-2xl sm:text-3xl font-mono font-black text-blue-700 tracking-wider bg-white px-3 py-1 rounded-xl border border-blue-300 shadow-xs">
                        {{ $otpCode ?? '123456' }}
                    </span>
                    <button type="button" onclick="copyOtpCode()" title="Sao chép mã" class="p-2 bg-white hover:bg-blue-100 text-blue-600 rounded-xl border border-blue-200 transition text-xs font-bold shadow-xs">
                        📋
                    </button>
                </div>
            </div>

            <!-- Hướng dẫn 2 bước -->
            <div class="text-[11px] text-slate-600 space-y-1.5 bg-white/80 p-3 rounded-xl border border-blue-100">
                <div class="flex items-start space-x-2">
                    <span class="w-4 h-4 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">1</span>
                    <span>Bấm nút <strong>"Mở Zalo Xác Thực"</strong> bên dưới để gửi tin nhắn cho Cửa hàng.</span>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="w-4 h-4 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">2</span>
                    <span>Nhập mã <strong>{{ $otpCode ?? '123456' }}</strong> vào 6 ô bên dưới và bấm nút Kích hoạt.</span>
                </div>
            </div>

            <!-- Nút Mở Zalo 1 Chạm -->
            <div class="space-y-2">
                <a href="{{ $zaloUrl ?? 'https://zalo.me/0974194305' }}" target="_blank" onclick="onOpenZaloClick()"
                   class="w-full py-3.5 px-4 bg-[#0068ff] hover:bg-[#0052cc] text-white font-black text-xs sm:text-sm rounded-xl shadow-md shadow-blue-500/25 transition flex items-center justify-center space-x-2 active:scale-98">
                    <span>💬 BẤM ĐỂ MỞ ZALO XÁC THỰC NGAY</span>
                    <span>→</span>
                </a>

                <button type="button" onclick="autoFillOtp()"
                        class="w-full py-2 px-3 bg-white hover:bg-slate-100 text-indigo-700 text-[11px] font-bold rounded-xl border border-slate-300 transition flex items-center justify-center space-x-1.5">
                    <span>⚡ Bấm vào đây để tự điền mã [ {{ $otpCode ?? '123456' }} ]</span>
                </button>
            </div>

            <!-- Quét mã QR nếu dùng máy tính -->
            <div class="pt-2 border-t border-blue-200/60 flex items-center justify-center space-x-3 text-center">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($zaloUrl ?? 'https://zalo.me/0974194305') }}"
                     alt="QR Zalo Hotline" class="w-16 h-16 rounded-xl border border-slate-300 bg-white p-1" />
                <div class="text-left text-[11px] text-slate-500 max-w-[240px]">
                    <strong class="text-slate-800 block">Nếu dùng máy tính:</strong>
                    Mở Zalo trên điện thoại quét mã QR bên cạnh để mở chat với Shop.
                </div>
            </div>

        </div>

        <!-- Error Alert (Client-side / AJAX) -->
        <div id="otp-error-alert" class="hidden p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-700 font-medium"></div>

        <!-- Form 6 ô số OTP -->
        <form id="otpForm" onsubmit="submitOtp(event)" class="space-y-5">
            <input type="hidden" id="phone" value="{{ $phone }}">

            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 text-center">
                    Nhập mã xác thực 6 số vào đây:
                </label>
                <div class="flex justify-center items-center gap-2 sm:gap-3" id="otp-inputs">
                    @for($i = 0; $i < 6; $i++)
                        <input type="text"
                               inputmode="numeric"
                               maxlength="1"
                               pattern="[0-9]"
                               data-index="{{ $i }}"
                               class="otp-digit w-11 h-13 sm:w-12 sm:h-14 text-center text-xl sm:text-2xl font-mono font-black rounded-2xl border-2 border-slate-300 bg-slate-50 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 focus:outline-none transition-all shadow-xs"
                               required
                               autocomplete="off" />
                    @endfor
                </div>
            </div>

            <!-- Nút bấm Xác Thực -->
            <button type="submit" id="btnVerify"
                    class="w-full py-3.5 px-5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-xs sm:text-sm font-black rounded-2xl shadow-md shadow-indigo-600/20 transition-all flex items-center justify-center space-x-2 active:scale-[0.98]">
                <span>XÁC NHẬN & KÍCH HOẠT TÀI KHOẢN</span>
                <span>→</span>
            </button>
        </form>

        <!-- Phần Chuyển hướng phụ -->
        <div class="pt-2 border-t border-slate-100 text-center space-y-2">
            <div>
                <a href="{{ route('customer.login') }}" class="text-xs text-slate-500 hover:text-indigo-600 font-semibold transition">
                    ← Quay lại trang đăng nhập
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const phone = document.getElementById('phone').value;
    const digits = document.querySelectorAll('.otp-digit');
    const errAlert = document.getElementById('otp-error-alert');
    const btnVerify = document.getElementById('btnVerify');
    const defaultOtp = "{{ $otpCode ?? '' }}";

    // Tự động focus ô đầu tiên
    if (digits.length > 0) {
        digits[0].focus();
    }

    // Tự động điền mã
    function autoFillOtp() {
        if (!defaultOtp) return;
        const chars = defaultOtp.split('');
        digits.forEach((d, i) => {
            if (chars[i]) d.value = chars[i];
        });
        checkAutoSubmit();
    }

    // Sao chép mã
    function copyOtpCode() {
        const code = document.getElementById('display-otp-code')?.textContent?.trim() || defaultOtp;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(code).then(() => {
                alert('Đã sao chép mã xác thực: ' + code);
            });
        }
    }

    // Khi bấm mở Zalo
    function onOpenZaloClick() {
        const code = document.getElementById('display-otp-code')?.textContent?.trim() || defaultOtp;
        if (navigator.clipboard) {
            navigator.clipboard.writeText('Xác thực tài khoản VPP: ' + code);
        }
    }

    // Xử lý chuyển ô tự động & Paste cả chuỗi 6 số
    digits.forEach((input, idx) => {
        input.addEventListener('input', (e) => {
            const val = e.target.value.replace(/\D/g, '');
            e.target.value = val ? val[val.length - 1] : '';

            if (e.target.value && idx < digits.length - 1) {
                digits[idx + 1].focus();
            }

            checkAutoSubmit();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && idx > 0) {
                digits[idx - 1].focus();
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
            if (pasted.length > 0) {
                pasted.split('').forEach((char, i) => {
                    if (digits[i]) digits[i].value = char;
                });
                const nextIdx = Math.min(pasted.length, digits.length - 1);
                digits[nextIdx].focus();
                checkAutoSubmit();
            }
        });
    });

    function getEnteredOtp() {
        let code = '';
        digits.forEach(d => code += d.value.trim());
        return code;
    }

    function checkAutoSubmit() {
        const code = getEnteredOtp();
        if (code.length === 6) {
            submitOtp();
        }
    }

    // Gửi xác thực OTP
    async function submitOtp(e) {
        if (e) e.preventDefault();
        const otpCode = getEnteredOtp();

        if (otpCode.length !== 6) {
            showError('Vui lòng nhập đủ 6 chữ số mã xác thực.');
            return;
        }

        hideError();
        btnVerify.disabled = true;
        btnVerify.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Đang kích hoạt tài khoản...';

        try {
            const res = await fetch("{{ route('otp.verify') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    phone: phone,
                    otp: otpCode,
                    action: 'verify',
                }),
            });

            const data = await res.json();

            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Mã xác thực không chính xác.');
            }

            btnVerify.classList.remove('from-indigo-600', 'to-indigo-700');
            btnVerify.classList.add('from-emerald-600', 'to-emerald-700');
            btnVerify.innerHTML = '<span>✓ Kích hoạt thành công! Đang chuyển hướng...</span>';

            setTimeout(() => {
                window.location.href = data.redirect_url || "{{ route('customer.profile') }}";
            }, 800);

        } catch (err) {
            showError(err.message);
            btnVerify.disabled = false;
            btnVerify.innerHTML = '<span>XÁC NHẬN & KÍCH HOẠT TÀI KHOẢN</span><span>→</span>';
            digits.forEach(d => d.value = '');
            digits[0].focus();
        }
    }

    function showError(msg) {
        errAlert.textContent = msg;
        errAlert.classList.remove('hidden');
    }

    function hideError() {
        errAlert.classList.add('hidden');
    }
</script>
@endpush
