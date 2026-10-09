@extends('layouts.storefront')

@section('title', 'Xác Thực Mã OTP | VPP & Dịch Vụ Máy In')
@section('meta_description', 'Nhập mã xác thực OTP 6 số để kích hoạt tài khoản khách hàng.')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-10 px-4 sm:px-6">
    <div class="w-full max-w-md bg-white rounded-3xl border border-slate-200/80 shadow-xl p-6 sm:p-8 space-y-6">

        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-2xl mx-auto shadow-xs">
                📲
            </div>
            <span class="inline-block px-3 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-black uppercase tracking-wider border border-emerald-200/50">
                BẢO MẬT TÀI KHOẢN
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900">
                Xác Thực Số Điện Thoại
            </h1>
            <p class="text-xs text-slate-500 leading-relaxed max-w-sm mx-auto">
                Hệ thống đã gửi mã xác thực gồm 6 chữ số đến số điện thoại
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

        @if(session('dev_otp'))
            <!-- Hộp hỗ trợ kiểm thử trong môi trường thử nghiệm -->
            <div class="p-3 rounded-2xl bg-sky-50 border border-sky-200 text-xs text-sky-900 font-medium flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase font-bold text-sky-600 block">Thử nghiệm hệ thống:</span>
                    <span>Mã OTP tự động sinh:</span>
                </div>
                <span class="font-mono font-black text-base px-2.5 py-1 bg-white rounded-xl border border-sky-300 text-sky-700 tracking-wider">
                    {{ session('dev_otp') }}
                </span>
            </div>
        @endif

        <!-- Error Alert (Client-side / AJAX) -->
        <div id="otp-error-alert" class="hidden p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-700 font-medium"></div>

        <!-- Form 6 ô số OTP -->
        <form id="otpForm" onsubmit="submitOtp(event)" class="space-y-6">
            <input type="hidden" id="phone" value="{{ $phone }}">

            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 text-center">
                    Nhập mã xác thực 6 chữ số:
                </label>
                <div class="flex justify-center items-center gap-2 sm:gap-3" id="otp-inputs">
                    @for($i = 0; $i < 6; $i++)
                        <input type="text"
                               inputmode="numeric"
                               maxlength="1"
                               pattern="[0-9]"
                               data-index="{{ $i }}"
                               class="otp-digit w-11 h-13 sm:w-12 sm:h-14 text-center text-xl sm:text-2xl font-mono font-black rounded-2xl border-2 border-slate-200 bg-slate-50/70 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 focus:outline-none transition-all shadow-xs"
                               required
                               autocomplete="off" />
                    @endfor
                </div>
            </div>

            <!-- Nút bấm Xác Thực -->
            <button type="submit" id="btnVerify"
                    class="w-full py-3.5 px-5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-xs font-black rounded-2xl shadow-md shadow-indigo-600/20 transition-all flex items-center justify-center space-x-2 active:scale-[0.98]">
                <span>XÁC NHẬN & KÍCH HOẠT TÀI KHOẢN</span>
                <span>→</span>
            </button>
        </form>

        <!-- Phần Gửi lại mã & Đếm ngược 60s -->
        <div class="pt-2 border-t border-slate-100 text-center space-y-3">
            <div id="countdown-box" class="text-xs text-slate-500 font-medium">
                Bạn chưa nhận được mã? Gửi lại sau
                <strong id="timer" class="font-mono font-bold text-indigo-600">60</strong>s
            </div>

            <div id="resend-actions" class="hidden space-y-2">
                <p class="text-xs text-slate-600 font-semibold">Chưa nhận được mã OTP? Chọn kênh nhận lại:</p>
                <div class="flex items-center justify-center gap-2">
                    <button type="button" onclick="resendOtp('zns')"
                            class="px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-xs font-bold border border-blue-200/80 transition flex items-center space-x-1.5">
                        <span>💬 Nhận qua Zalo ZNS</span>
                    </button>
                    <button type="button" onclick="resendOtp('sms')"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold border border-slate-200 transition flex items-center space-x-1.5">
                        <span>📩 Nhận qua SMS</span>
                    </button>
                </div>
            </div>

            <div>
                <a href="{{ route('customer.login') }}" class="text-[11px] text-slate-400 hover:text-indigo-600 font-semibold transition">
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
    const timerEl = document.getElementById('timer');
    const countdownBox = document.getElementById('countdown-box');
    const resendActions = document.getElementById('resend-actions');
    const errAlert = document.getElementById('otp-error-alert');
    const btnVerify = document.getElementById('btnVerify');

    let countdownSeconds = 60;
    let timerInterval = null;

    // Tự động focus ô đầu tiên
    if (digits.length > 0) {
        digits[0].focus();
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

    // Đếm ngược 60s
    function startCountdown(seconds = 60) {
        clearInterval(timerInterval);
        countdownSeconds = seconds;
        countdownBox.classList.remove('hidden');
        resendActions.classList.add('hidden');
        timerEl.textContent = countdownSeconds;

        timerInterval = setInterval(() => {
            countdownSeconds--;
            timerEl.textContent = countdownSeconds;
            if (countdownSeconds <= 0) {
                clearInterval(timerInterval);
                countdownBox.classList.add('hidden');
                resendActions.classList.remove('hidden');
            }
        }, 1000);
    }

    startCountdown(60);

    // Gửi xác thực OTP
    async function submitOtp(e) {
        if (e) e.preventDefault();
        const otpCode = getEnteredOtp();

        if (otpCode.length !== 6) {
            showError('Vui lòng nhập đủ 6 chữ số mã OTP.');
            return;
        }

        hideError();
        btnVerify.disabled = true;
        btnVerify.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Đang kiểm tra mã...';

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
            btnVerify.innerHTML = '<span>✓ Xác thực thành công! Đang chuyển hướng...</span>';

            setTimeout(() => {
                window.location.href = data.redirect_url || "{{ route('customer.profile') }}";
            }, 800);

        } catch (err) {
            showError(err.message);
            btnVerify.disabled = false;
            btnVerify.innerHTML = '<span>XÁC NHẬN & KÍCH HOẠT TÀI KHOẢN</span><span>→</span>';
            // Xóa các ô và focus lại ô đầu
            digits.forEach(d => d.value = '');
            digits[0].focus();
        }
    }

    // Yêu cầu gửi lại OTP
    async function resendOtp(channel = 'zns') {
        hideError();
        try {
            const res = await fetch("{{ route('otp.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    phone: phone,
                    channel: channel,
                    action: 'verify',
                }),
            });

            const data = await res.json();

            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Chưa thể gửi lại mã OTP lúc này.');
            }

            alert(data.message || 'Đã gửi lại mã OTP thành công!');
            if (data.dev_otp) {
                console.log('Mã OTP thử nghiệm mới:', data.dev_otp);
            }

            startCountdown(data.cooldown_seconds || 60);

        } catch (err) {
            showError(err.message);
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
