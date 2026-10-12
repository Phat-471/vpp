@extends('layouts.storefront')

@section('title', ($action === 'register' ? 'Hoàn tất đăng ký' : 'Xác minh số điện thoại') . ' | VPP & Dịch Vụ Máy In')
@section('meta_description', 'Nhập mã xác thực để tiếp tục.')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-8 px-4 sm:px-6">
    <div class="w-full max-w-lg bg-white rounded-3xl border border-slate-200/80 shadow-2xl p-6 sm:p-8 space-y-6">

        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 text-[#0068ff] flex items-center justify-center text-3xl mx-auto shadow-xs font-black">
                <x-heroicon-o-device-phone-mobile class="w-7 h-7" aria-hidden="true" />
            </div>
            <span class="inline-block px-3 py-0.5 rounded-full bg-blue-50 text-[#0068ff] text-[11px] font-black uppercase tracking-wider border border-blue-200/50">
                XÁC MINH SỐ ĐIỆN THOẠI
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900">
                {{ $action === 'register' ? 'Hoàn tất đăng ký' : 'Xác minh tài khoản' }}
            </h1>
            <p class="text-xs text-slate-500 leading-relaxed max-w-sm mx-auto">
                Mã xác thực sẽ gửi đến
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

        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
            <p class="text-sm text-slate-700">Gửi mã một lần để tiếp tục xác minh.</p>
            <button type="button" id="btnSendOtp" onclick="requestOtp()"
                class="w-full min-h-[44px] px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl transition">
                Gửi mã xác thực
            </button>
            <p id="otp-send-status" class="text-xs text-slate-600" role="status" aria-live="polite">Số nhận mã: {{ $maskedPhone }}</p>
            @if(app()->environment(['local', 'testing']))
                <p id="dev-otp-notice" class="hidden text-xs font-mono font-bold text-amber-800" role="status"></p>
                <button type="button" id="btnFillDevOtp" onclick="autoFillOtp()" class="hidden w-full min-h-[44px] px-4 bg-white text-slate-700 border border-slate-300 font-semibold text-xs rounded-xl">
                    Điền mã thử nghiệm
                </button>
            @endif
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
                <span>{{ $action === 'register' ? 'XÁC NHẬN ĐĂNG KÝ' : 'XÁC NHẬN SỐ ĐIỆN THOẠI' }}</span>
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
    let defaultOtp = '';
    const sendStatus = document.getElementById('otp-send-status');
    const sendButton = document.getElementById('btnSendOtp');
    const devOtpNotice = document.getElementById('dev-otp-notice');
    const fillDevOtpButton = document.getElementById('btnFillDevOtp');

    async function requestOtp() {
        sendButton.disabled = true;
        sendStatus.textContent = 'Đang gửi mã…';

        try {
            const response = await fetch("{{ route('otp.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({ phone, channel: 'zns', action: @json($action) }),
            });
            const data = await response.json();

            if (!response.ok || !data.success) {
                const error = new Error(data.message || 'Không gửi được mã. Vui lòng thử lại.');
                error.waitSeconds = data.wait_seconds || 0;
                throw error;
            }

            sendStatus.textContent = data.message || 'Mã xác thực đã được gửi.';
            if (data.dev_otp && devOtpNotice && fillDevOtpButton) {
                defaultOtp = String(data.dev_otp);
                devOtpNotice.textContent = `Mã thử nghiệm: ${defaultOtp}`;
                devOtpNotice.classList.remove('hidden');
                fillDevOtpButton.classList.remove('hidden');
            }

            startSendCooldown(data.cooldown_seconds || 60);
        } catch (error) {
            sendStatus.textContent = error.message;
            if (error.waitSeconds) {
                startSendCooldown(error.waitSeconds);
            } else {
                sendButton.disabled = false;
                sendButton.textContent = 'Gửi mã xác thực';
            }
        }
    }

    function startSendCooldown(seconds) {
        let remaining = Math.max(1, Number(seconds) || 60);
        sendButton.disabled = true;
        sendButton.textContent = `Gửi lại sau ${remaining}s`;
        const timer = setInterval(() => {
            remaining--;
            if (remaining <= 0) {
                clearInterval(timer);
                sendButton.disabled = false;
                sendButton.textContent = 'Gửi lại mã';
                return;
            }
            sendButton.textContent = `Gửi lại sau ${remaining}s`;
        }, 1000);
    }

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
        btnVerify.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Đang xác minh...';

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
                    action: @json($action),
                }),
            });

            const data = await res.json();

            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Mã xác thực không chính xác.');
            }

            btnVerify.classList.remove('from-indigo-600', 'to-indigo-700');
            btnVerify.classList.add('from-emerald-600', 'to-emerald-700');
            btnVerify.innerHTML = '<span>✓ Thành công. Đang chuyển hướng...</span>';

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
