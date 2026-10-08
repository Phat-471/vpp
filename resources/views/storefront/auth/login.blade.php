@extends('layouts.storefront')

@section('title', 'Đăng Nhập Khách Hàng | VPP & Dịch Vụ Máy In')
@section('meta_description', 'Đăng nhập để theo dõi đơn hàng và tra cứu lịch sử sửa máy in của bạn tại Cửa hàng VPP & Dịch Vụ Máy In.')

@section('content')
    <div class="max-w-md mx-auto px-4 py-8 sm:py-16 space-y-6">

        <div class="text-center space-y-2">
            <div
                class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-700 to-amber-500 text-white flex items-center justify-center font-black text-xl mx-auto shadow-sm">
                VP
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900">Đăng Nhập Khách Hàng</h1>
            <p class="text-xs text-slate-500">
                Nhập số điện thoại hoặc quét mã Zalo 1 chạm để quản lý tài khoản của bạn.
            </p>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-5">

            <!-- NÚT ĐĂNG NHẬP NHANH QUA ZALO -->
            <div class="p-4 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-md space-y-3">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-white text-blue-600 flex items-center justify-center font-black text-xs shadow-xs shrink-0">
                        Zalo
                    </div>
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wide">Đăng nhập nhanh bằng Zalo</h3>
                        <p class="text-[11px] text-blue-100 mt-0.5">Không cần gõ mật khẩu • Nhận diện tức thì</p>
                    </div>
                </div>
                <button type="button" onclick="openZaloModal()"
                    class="w-full py-2.5 px-4 rounded-xl bg-white hover:bg-blue-50 text-blue-700 font-extrabold text-xs shadow-sm transition flex items-center justify-center space-x-2 active:scale-98">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    <span>Quét Mã QR Zalo Để Đăng Nhập</span>
                </button>
            </div>

            <div class="relative flex items-center justify-center my-4">
                <div class="border-t border-slate-200 w-full"></div>
                <span class="bg-white px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider relative">Hoặc dùng
                    số điện thoại</span>
            </div>

            <!-- FORM ĐĂNG NHẬP TRUYỀN THỐNG -->
            <form action="{{ route('customer.post-login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Số điện thoại *</label>
                    <input type="tel" name="phone" required value="{{ old('phone') }}" placeholder="VD: 0901 234 567"
                        class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Mật khẩu *</label>
                    <input type="password" name="password" required placeholder="Nhập mật khẩu..."
                        class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center space-x-2 text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 accent-indigo-600 rounded" />
                        <span>Ghi nhớ đăng nhập</span>
                    </label>
                    <a href="{{ route('lookup.index') }}" class="text-indigo-600 hover:underline">
                        Quên mật khẩu?
                    </a>
                </div>

                <button type="submit" id="btnLoginSubmit"
                    class="w-full py-3.5 px-4 bg-slate-800 hover:bg-slate-900 text-white font-black text-xs rounded-xl shadow-xs transition">
                    ĐĂNG NHẬP BẰNG MẬT KHẨU
                </button>
            </form>

            <div class="pt-2 border-t border-slate-100 text-center text-xs text-slate-500">
                <span>Chưa có tài khoản?</span>
                <a href="{{ route('customer.register') }}" class="font-bold text-indigo-600 hover:underline ml-1">Đăng ký
                    tài khoản mới</a>
            </div>

        </div>

    </div>

    <!-- MODAL POPUP QUÉT MÃ QR ZALO NATIVE -->
    <div id="zaloModal"
        class="hidden fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div
            class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-sm w-full p-6 text-center relative animate-fade-in space-y-4">

            <!-- Nút Đóng -->
            <button type="button" onclick="closeZaloModal()"
                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-bold transition">
                ✕
            </button>

            <div
                class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-black text-sm mx-auto shadow-sm">
                Zalo
            </div>

            <div>
                <h3 class="text-base font-black text-slate-900">Đăng Nhập Nhanh Qua Zalo</h3>
                <p class="text-[11px] text-slate-500 mt-1">Quét mã bằng app Zalo ➔ Bấm "Chia sẻ" để đăng nhập ngay lập tức
                </p>
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
            <div id="statusSuccess"
                class="hidden p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold">
                ✓ Xác thực thành công! Đang chuyển hướng...
            </div>

            <!-- Nút Giả Lập Thử Nghiệm Ngay -->
            <div class="pt-2 border-t border-slate-100">
                <button type="button" onclick="mockScanZalo()"
                    class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition flex items-center justify-center space-x-1.5">
                    <span>🔄 Bấm Giả Lập Khách Quét Xong (Test)</span>
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
                    document.getElementById('statusSuccess').innerText = `✓ Đăng nhập thành công: ${data.data.customer_name}!`;
                    setTimeout(() => {
                        window.location.href = data.redirect_url || "{{ route('customer.profile') }}";
                    }, 1000);
                }
            } catch (e) {
                alert('Lỗi giả lập, vui lòng thử lại.');
            }
        }
    </script>
@endsection