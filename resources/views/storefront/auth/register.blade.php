@extends('layouts.storefront')

@section('title', 'Đăng Ký Tài Khoản Khách Hàng | VPP & Dịch Vụ Máy In')
@section('meta_description', 'Tạo tài khoản khách hàng để lưu địa chỉ nhận hàng, theo dõi lịch sử đơn hàng và tiến độ sửa máy in.')

@section('content')
<div class="max-w-md mx-auto px-4 py-8 sm:py-14 space-y-6">

    <div class="text-center space-y-2">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-700 to-amber-500 text-white flex items-center justify-center font-black text-xl mx-auto shadow-sm">
            VP
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900">Đăng Ký Tài Khoản Mới</h1>
        <p class="text-xs text-slate-500">
            Quản lý đơn hàng, lưu địa chỉ giao hàng và tra cứu lịch sử sửa máy in dễ dàng.
        </p>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-5">
        
        <!-- NÚT ĐĂNG KÝ SIÊU TỐC QUA MÃ QR ZALO -->
        <div class="p-4.5 rounded-2xl bg-gradient-to-br from-blue-600 via-indigo-600 to-blue-700 text-white shadow-md space-y-3">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-white text-blue-600 flex items-center justify-center font-black text-xs shadow-xs shrink-0">
                    Zalo
                </div>
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wide flex items-center space-x-1.5">
                        <span>Đăng ký 1 chạm bằng Zalo</span>
                        <span class="px-1.5 py-0.2 rounded bg-amber-400 text-slate-950 font-black text-[9px]">KHUYÊN DÙNG</span>
                    </h3>
                    <p class="text-[11px] text-blue-100 mt-0.5">Xác thực SĐT chính chủ • Không cần OTP SMS</p>
                </div>
            </div>
            <button type="button" onclick="openZaloModal()" class="w-full py-2.5 px-4 rounded-xl bg-white hover:bg-blue-50 text-blue-700 font-extrabold text-xs shadow-sm transition flex items-center justify-center space-x-2 active:scale-98">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                <span>Quét Mã QR Zalo Để Đăng Ký Ngay</span>
            </button>
        </div>

        <!-- HIỂN THỊ THÔNG BÁO LỖI SERVER-SIDE NẾU CÓ -->
        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center space-x-2">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold space-y-1.5">
                <div class="font-bold flex items-center space-x-1.5 text-rose-800">
                    <span>⚠️</span>
                    <span>Thông tin chưa chính xác, vui lòng kiểm tra lại:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-[11px] pl-1 font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="relative flex items-center justify-center my-4">
            <div class="border-t border-slate-200 w-full"></div>
            <span class="bg-white px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider relative">Hoặc điền form thủ công</span>
        </div>

        <!-- FORM ĐIỀN THỦ CÔNG (ĐƯỢC BẢO VỆ 2 LỚP CHỐNG SPAM & DỮ LIỆU RÁC) -->
        <form id="registerForm" action="{{ route('customer.post-register') }}" method="POST" class="space-y-4" novalidate>
            @csrf

            <!-- Honeypot chống bot spam (ẩn hoàn toàn, bot tự điền sẽ bị chặn) -->
            <div style="display:none !important;" aria-hidden="true">
                <input type="text" name="website" tabindex="-1" autocomplete="off" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Họ và tên của bạn *</label>
                <input type="text" id="regName" name="name" required minlength="2" maxlength="100" value="{{ old('name') }}" placeholder="VD: Nguyễn Văn An" class="w-full text-xs px-3.5 py-2.5 rounded-xl border @error('name') border-rose-400 bg-rose-50/30 @else border-slate-300 bg-slate-50 @enderror focus:ring-2 focus:ring-indigo-600 font-medium" />
                @error('name')
                    <p class="mt-1 text-[11px] font-bold text-rose-600">{{ $message }}</p>
                @enderror
                <p id="nameErrorClient" class="mt-1 text-[11px] font-bold text-rose-600 hidden"></p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Số điện thoại đăng nhập (10 số Việt Nam) *</label>
                <input type="tel" id="regPhone" name="phone" required maxlength="10" value="{{ old('phone') }}" placeholder="VD: 0901234567" class="w-full text-xs px-3.5 py-2.5 rounded-xl border @if($errors->has('phone') || $errors->has('cleaned_phone')) border-rose-400 bg-rose-50/30 @else border-slate-300 bg-slate-50 @enderror focus:ring-2 focus:ring-indigo-600 font-mono font-medium" />
                @error('phone')
                    <p class="mt-1 text-[11px] font-bold text-rose-600">{{ $message }}</p>
                @enderror
                @error('cleaned_phone')
                    <p class="mt-1 text-[11px] font-bold text-rose-600">{{ $message }}</p>
                @enderror
                <p id="phoneErrorClient" class="mt-1 text-[11px] font-bold text-rose-600 hidden"></p>
                <p id="phoneHelp" class="mt-1 text-[10px] text-slate-400">Đầu số: 03, 05, 07, 08, 09 (gồm đúng 10 chữ số)</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Email (tùy chọn)</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="VD: vanan@gmail.com" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
                @error('email')
                    <p class="mt-1 text-[11px] font-bold text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Địa chỉ giao hàng mặc định (tùy chọn)</label>
                <input type="text" name="address" value="{{ old('address') }}" placeholder="VD: 45 Lê Duẩn, Q.1, TP.HCM" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Mật khẩu (tối thiểu 6 ký tự) *</label>
                <input type="password" id="regPassword" name="password" required minlength="6" placeholder="Nhập ít nhất 6 ký tự..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border @error('password') border-rose-400 bg-rose-50/30 @else border-slate-300 bg-slate-50 @enderror focus:ring-2 focus:ring-indigo-600 font-medium" />
                @error('password')
                    <p class="mt-1 text-[11px] font-bold text-rose-600">{{ $message }}</p>
                @enderror
                <p id="passwordLenClient" class="mt-1 text-[11px] font-bold text-rose-600 hidden"></p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Xác nhận mật khẩu *</label>
                <input type="password" id="regPasswordConfirm" name="password_confirmation" required minlength="6" placeholder="Nhập lại chính xác mật khẩu..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
                <p id="passwordMatchClient" class="mt-1 text-[11px] font-bold hidden"></p>
            </div>

            <button type="submit" id="btnRegisterSubmit" class="w-full py-3.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-xs transition flex items-center justify-center space-x-2 active:scale-98">
                <span>TIẾP TỤC: QUÉT MÃ QR ZALO ĐỂ XÁC THỰC</span>
                <span>→</span>
            </button>
        </form>

        <div class="pt-2 border-t border-slate-100 text-center text-xs text-slate-500">
            <span>Đã có tài khoản?</span>
            <a href="{{ route('customer.login') }}" class="font-bold text-indigo-600 hover:underline ml-1">Đăng nhập tại đây</a>
        </div>

    </div>

</div>

<!-- MODAL POPUP QUÉT MÃ QR ZALO NATIVE -->
<div id="zaloModal" class="hidden fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-sm w-full p-6 text-center relative animate-fade-in space-y-4">
        
        <!-- Nút Đóng -->
        <button type="button" onclick="closeZaloModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-bold transition">
            ✕
        </button>

        <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-black text-sm mx-auto shadow-sm">
            Zalo
        </div>

        <div>
            <h3 class="text-base font-black text-slate-900">Quét Mã QR Bằng Zalo</h3>
            <p class="text-[11px] text-slate-500 mt-1">Mở app Zalo trên điện thoại ➔ Bấm Quét mã ➔ Bấm "Chia sẻ" để xác thực 1 chạm</p>
        </div>

        <!-- Khung Chứa Mã QR -->
        <div class="p-3 bg-slate-50 border-2 border-blue-500/30 rounded-2xl inline-block relative mx-auto">
            <div id="qrLoading" class="w-48 h-48 flex flex-col items-center justify-center space-y-2 text-slate-400">
                <div class="w-8 h-8 border-3 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-[10px] font-bold">Đang tạo mã QR...</span>
            </div>
            <img id="qrImage" src="" alt="Mã QR Zalo" class="hidden w-48 h-48 rounded-xl object-contain" />
        </div>

        <!-- Đồng Hồ Đếm Ngược & Trạng Thái -->
        <div id="timerBox" class="text-[11px] text-slate-500 flex items-center justify-center space-x-1 font-medium">
            <span>Mã có hiệu lực trong:</span>
            <strong id="timerCountdown" class="text-amber-600 font-mono font-bold text-xs">05:00</strong>
        </div>

        <!-- Trạng thái xác thực thành công -->
        <div id="statusSuccess" class="hidden p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold">
            ✓ Xác thực thành công! Đang chuyển hướng...
        </div>

        <!-- Nút Giả Lập Thử Nghiệm Ngay (Dành cho Demo) -->
        <div class="pt-2 border-t border-slate-100">
            <button type="button" onclick="mockScanZalo()" class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition flex items-center justify-center space-x-1.5">
                <span>🔄 Bấm Giả Lập Khách Quét QR Xong (Test)</span>
            </button>
        </div>

    </div>
</div>

<script>
    let currentToken = null;
    let pollInterval = null;
    let countdownInterval = null;
    let secondsLeft = 300;

    async function openZaloModal() {
        document.getElementById('zaloModal').classList.remove('hidden');
        document.getElementById('qrLoading').classList.remove('hidden');
        document.getElementById('qrImage').classList.add('hidden');
        document.getElementById('statusSuccess').classList.add('hidden');

        try {
            const res = await fetch("{{ route('zalo.verify.init') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const result = await res.json();
            if (result.success && result.data) {
                currentToken = result.data.token;
                document.getElementById('qrImage').src = result.data.qr_image_url;
                document.getElementById('qrImage').onload = () => {
                    document.getElementById('qrLoading').classList.add('hidden');
                    document.getElementById('qrImage').classList.remove('hidden');
                };

                startCountdown(result.data.seconds_remaining || 300);
                startPolling();
            }
        } catch (e) {
            alert('Không thể tạo mã QR Zalo, vui lòng thử lại.');
        }
    }

    function closeZaloModal() {
        document.getElementById('zaloModal').classList.add('hidden');
        clearInterval(pollInterval);
        clearInterval(countdownInterval);
    }

    function startCountdown(seconds) {
        clearInterval(countdownInterval);
        secondsLeft = Math.floor(seconds);
        updateTimerDisplay();
        countdownInterval = setInterval(() => {
            secondsLeft--;
            if (secondsLeft <= 0) {
                clearInterval(countdownInterval);
                clearInterval(pollInterval);
                document.getElementById('timerBox').innerHTML = '<span class="text-rose-600 font-bold">Mã đã hết hạn, vui lòng mở lại</span>';
            } else {
                updateTimerDisplay();
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        const m = Math.floor(secondsLeft / 60);
        const s = secondsLeft % 60;
        document.getElementById('timerCountdown').innerText = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    }

    function startPolling() {
        clearInterval(pollInterval);
        pollInterval = setInterval(async () => {
            if (!currentToken) return;
            try {
                const res = await fetch(`{{ url('/zalo-auth/check') }}/${currentToken}`);
                const data = await res.json();
                if (data.success && data.data && data.data.verified) {
                    clearInterval(pollInterval);
                    clearInterval(countdownInterval);
                    document.getElementById('statusSuccess').classList.remove('hidden');
                    document.getElementById('statusSuccess').innerText = `✓ Chào mừng ${data.data.customer_name}! Đang đăng nhập...`;
                    setTimeout(() => {
                        window.location.href = data.data.redirect_url || "{{ route('customer.profile') }}";
                    }, 1200);
                }
            } catch (err) {
                console.error(err);
            }
        }, 2000);
    }

    async function mockScanZalo() {
        if (!currentToken) return;
        try {
            const res = await fetch("{{ route('zalo.verify.mock-confirm') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ token: currentToken })
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('statusSuccess').classList.remove('hidden');
                document.getElementById('statusSuccess').innerText = `✓ Xác thực thành công: ${data.data.customer_name}!`;
                setTimeout(() => {
                    window.location.href = data.redirect_url || "{{ route('customer.profile') }}";
                }, 1000);
            }
        } catch (e) {
            alert('Lỗi giả lập, vui lòng thử lại.');
        }
    }
    // CLIENT-SIDE VALIDATION TỨC THÌ (LỚP 1 BẢO VỆ CHỐNG SPAM / DỮ LIỆU RÁC)
    const regForm = document.getElementById('registerForm');
    const nameInput = document.getElementById('regName');
    const phoneInput = document.getElementById('regPhone');
    const passInput = document.getElementById('regPassword');
    const passConfirmInput = document.getElementById('regPasswordConfirm');

    const nameErr = document.getElementById('nameErrorClient');
    const phoneErr = document.getElementById('phoneErrorClient');
    const passLenErr = document.getElementById('passwordLenClient');
    const passMatchMsg = document.getElementById('passwordMatchClient');

    // 1. Kiểm tra SĐT Việt Nam (Đầu 03, 05, 07, 08, 09 - Đủ 10 số)
    function checkPhoneValid(val) {
        const clean = val.replace(/\D/g, '');
        if (clean.length === 0) return { valid: false, msg: 'Vui lòng nhập số điện thoại.' };
        if (clean.length !== 10) return { valid: false, msg: `Số điện thoại phải đủ 10 số (hiện có ${clean.length} số).` };
        const vnPrefixRegex = /^(03|05|07|08|09)/;
        if (!vnPrefixRegex.test(clean)) {
            return { valid: false, msg: 'Đầu số không hợp lệ! Phải là 03, 05, 07, 08 hoặc 09.' };
        }
        return { valid: true, msg: '✓ Số điện thoại hợp lệ' };
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            // Chỉ cho phép gõ số
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
            if (this.value.length > 0) {
                const res = checkPhoneValid(this.value);
                if (!res.valid) {
                    phoneErr.classList.remove('hidden', 'text-emerald-600');
                    phoneErr.classList.add('text-rose-600');
                    phoneErr.innerText = res.msg;
                } else {
                    phoneErr.classList.remove('hidden', 'text-rose-600');
                    phoneErr.classList.add('text-emerald-600');
                    phoneErr.innerText = res.msg;
                }
            } else {
                phoneErr.classList.add('hidden');
            }
        });
    }

    // 2. Kiểm tra Họ Tên
    if (nameInput) {
        nameInput.addEventListener('input', function() {
            if (this.value.trim().length > 0 && this.value.trim().length < 2) {
                nameErr.classList.remove('hidden');
                nameErr.innerText = 'Họ và tên tối thiểu 2 ký tự.';
            } else {
                nameErr.classList.add('hidden');
            }
        });
    }

    // 3. Kiểm tra Độ Dài Mật Khẩu
    if (passInput) {
        passInput.addEventListener('input', function() {
            if (this.value.length > 0 && this.value.length < 6) {
                passLenErr.classList.remove('hidden');
                passLenErr.innerText = 'Mật khẩu phải có ít nhất 6 ký tự.';
            } else {
                passLenErr.classList.add('hidden');
            }
            checkPasswordMatch();
        });
    }

    // 4. Kiểm tra Khớp 2 Mật Khẩu
    function checkPasswordMatch() {
        if (!passConfirmInput || !passInput) return;
        const p1 = passInput.value;
        const p2 = passConfirmInput.value;
        if (p2.length === 0) {
            passMatchMsg.classList.add('hidden');
            return;
        }
        if (p1 !== p2) {
            passMatchMsg.classList.remove('hidden', 'text-emerald-600');
            passMatchMsg.classList.add('text-rose-600');
            passMatchMsg.innerText = '⚠️ Mật khẩu xác nhận không khớp!';
        } else {
            passMatchMsg.classList.remove('hidden', 'text-rose-600');
            passMatchMsg.classList.add('text-emerald-600');
            passMatchMsg.innerText = '✓ Mật khẩu hoàn toàn trùng khớp';
        }
    }

    if (passConfirmInput) {
        passConfirmInput.addEventListener('input', checkPasswordMatch);
    }

    // 5. Chặn Submit nếu form còn lỗi
    if (regForm) {
        regForm.addEventListener('submit', function(e) {
            // Kiểm tra họ tên
            if (nameInput.value.trim().length < 2) {
                e.preventDefault();
                nameErr.classList.remove('hidden');
                nameErr.innerText = 'Vui lòng nhập họ và tên hợp lệ (từ 2 ký tự).';
                nameInput.focus();
                return false;
            }

            // Kiểm tra số điện thoại
            const phoneRes = checkPhoneValid(phoneInput.value);
            if (!phoneRes.valid) {
                e.preventDefault();
                phoneErr.classList.remove('hidden', 'text-emerald-600');
                phoneErr.classList.add('text-rose-600');
                phoneErr.innerText = phoneRes.msg;
                phoneInput.focus();
                return false;
            }

            // Kiểm tra mật khẩu
            if (passInput.value.length < 6) {
                e.preventDefault();
                passLenErr.classList.remove('hidden');
                passLenErr.innerText = 'Mật khẩu phải có tối thiểu 6 ký tự.';
                passInput.focus();
                return false;
            }

            // Kiểm tra mật khẩu xác nhận
            if (passInput.value !== passConfirmInput.value) {
                e.preventDefault();
                passMatchMsg.classList.remove('hidden', 'text-emerald-600');
                passMatchMsg.classList.add('text-rose-600');
                passMatchMsg.innerText = '⚠️ Hai mật khẩu chưa khớp nhau. Vui lòng kiểm tra lại!';
                passConfirmInput.focus();
                return false;
            }
        });
    }
</script>
@endsection
