@extends('layouts.storefront')

@section('title', 'Xác Thực Số Điện Thoại Qua Zalo | VPP & Dịch Vụ Máy In')
@section('meta_description', 'Quét mã QR bằng ứng dụng Zalo để xác thực số điện thoại chính chủ cho tài khoản của bạn.')

@section('content')
<div class="max-w-xl mx-auto px-4 py-8 sm:py-14 space-y-6">

    <!-- CÁC BƯỚC ĐĂNG KÝ (PROGRESS STEP) -->
    <div class="flex items-center justify-center space-x-3 text-xs font-bold">
        <div class="flex items-center space-x-1.5 text-emerald-600">
            <span class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center text-[11px] font-black">✓</span>
            <span>1. Điền Thông Tin</span>
        </div>
        <span class="text-slate-300">───</span>
        <div class="flex items-center space-x-1.5 text-blue-600 font-extrabold">
            <span class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center text-[11px] font-black animate-pulse">2</span>
            <span>2. Quét QR Zalo</span>
        </div>
        <span class="text-slate-300">───</span>
        <div class="flex items-center space-x-1.5 text-slate-400">
            <span class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center text-[11px] font-bold">3</span>
            <span>3. Hoàn Tất</span>
        </div>
    </div>

    <!-- KHUNG CHÍNH HIỂN THỊ MÃ QR -->
    <div class="bg-white p-6 sm:p-10 rounded-3xl border border-slate-200 shadow-sm text-center space-y-5">

        <!-- Header -->
        <div class="space-y-1">
            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-black text-sm mx-auto shadow-sm">
                Zalo
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 pt-2">Xác Thực Số Điện Thoại</h1>
            <p class="text-xs text-slate-500 max-w-md mx-auto">
                Vui lòng mở ứng dụng <strong>Zalo</strong> trên điện thoại và quét mã QR bên dưới để xác thực số điện thoại chính chủ.
            </p>
        </div>

        <!-- Box tóm tắt tài khoản vừa tạo -->
        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs flex items-center justify-around">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Họ và tên</span>
                <strong class="text-slate-800 text-sm">{{ $customer->name ?? $verification->name ?? 'Khách hàng' }}</strong>
            </div>
            <div class="w-px h-8 bg-slate-200"></div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Số điện thoại xác thực</span>
                <strong class="text-blue-600 font-mono text-sm">{{ $customer->phone ?? $verification->phone }}</strong>
            </div>
        </div>

        <!-- KHUNG MÃ QR CODE TO RÕ -->
        <div class="relative inline-block p-4 bg-white rounded-3xl border-3 border-blue-500 shadow-md">
            <img id="qrImage" src="{{ $qrData['qr_image_url'] }}" alt="Mã QR Zalo" class="w-56 h-56 sm:w-64 sm:h-64 rounded-2xl object-contain mx-auto" />
            
            <!-- Logo Zalo nhỏ góc trên -->
            <div class="mt-2 text-[11px] text-blue-600 font-bold flex items-center justify-center space-x-1">
                <span>⚡ Quét bằng app Zalo để xác thực</span>
            </div>
        </div>

        <!-- ĐỒNG HỒ ĐẾM NGƯỢC -->
        <div id="timerBox" class="text-xs text-slate-500 flex items-center justify-center space-x-1.5 font-medium">
            <span>Mã QR có hiệu lực trong:</span>
            <strong id="timerCountdown" class="text-amber-600 font-mono font-bold text-sm">05:00</strong>
        </div>

        <!-- THÔNG BÁO KHI XÁC THỰC THÀNH CÔNG -->
        <div id="statusSuccess" class="hidden p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs font-bold animate-bounce-once">
            <div class="text-2xl mb-1 text-emerald-600">✓</div>
            <div class="text-sm">XÁC THỰC ZALO THÀNH CÔNG!</div>
            <p class="text-slate-600 mt-1 font-normal">Hệ thống đang tự động đăng nhập và đưa bạn vào tài khoản...</p>
        </div>

        <!-- HƯỚNG DẪN 3 BƯỚC NHANH -->
        <div class="text-left bg-blue-50/60 rounded-2xl p-4 border border-blue-100 text-xs text-slate-600 space-y-2">
            <div class="font-bold text-blue-900 flex items-center space-x-1.5">
                <span>📱 Cách thực hiện trên điện thoại:</span>
            </div>
            <div class="flex items-start space-x-2">
                <span class="w-4 h-4 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">1</span>
                <span>Mở app <strong>Zalo</strong> trên điện thoại của bạn.</span>
            </div>
            <div class="flex items-start space-x-2">
                <span class="w-4 h-4 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">2</span>
                <span>Bấm vào biểu tượng <strong>Quét mã QR</strong> ở góc trên bên phải màn hình Zalo.</span>
            </div>
            <div class="flex items-start space-x-2">
                <span class="w-4 h-4 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">3</span>
                <span>Bấm nút <strong>"Chia sẻ số điện thoại"</strong> để hoàn tất ngay lập tức.</span>
            </div>
        </div>

        <!-- NÚT GIẢ LẬP TEST THỬ NGHIỆM TỨC THÌ (CHO MÔI TRƯỜNG LOCALHOST / DEV) -->
        <div class="pt-4 border-t border-slate-100 space-y-2.5">
            <div class="p-3 bg-amber-50 rounded-2xl border border-amber-200 text-left">
                <div class="flex items-center space-x-2 text-amber-800 text-xs font-bold">
                    <span>💡</span>
                    <span>Đang chạy trên môi trường Local (127.0.0.1):</span>
                </div>
                <p class="text-[11px] text-amber-700 mt-1 leading-relaxed">
                    Do đang chạy cục bộ trên máy tính, điện thoại thật sẽ không thể truy cập link nội bộ này. Anh có thể bấm ngay nút bên dưới để <strong>giả lập quét mã và kích hoạt tài khoản thành công</strong> ngay lập tức:
                </p>
            </div>

            <button type="button" onclick="mockScanZalo()" class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md shadow-emerald-600/20 transition flex items-center justify-center space-x-2 active:scale-98">
                <span>⚡ BẤM ĐÂY ĐỂ KÍCH HOẠT TÀI KHOẢN NGAY (TEST LOCAL)</span>
                <span>✓</span>
            </button>
        </div>

    </div>

</div>

<script>
    const token = "{{ $verification->token }}";
    let secondsLeft = Math.floor({{ $qrData['seconds_remaining'] ?? 300 }});
    let countdownInterval = null;
    let pollInterval = null;

    function updateTimer() {
        const m = Math.floor(secondsLeft / 60);
        const s = secondsLeft % 60;
        document.getElementById('timerCountdown').innerText = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    }

    countdownInterval = setInterval(() => {
        secondsLeft--;
        if (secondsLeft <= 0) {
            clearInterval(countdownInterval);
            clearInterval(pollInterval);
            document.getElementById('timerBox').innerHTML = '<span class="text-rose-600 font-bold">Mã QR đã hết hạn. <a href="{{ route("customer.register") }}" class="underline text-blue-600 ml-1">Đăng ký lại</a></span>';
        } else {
            updateTimer();
        }
    }, 1000);
    updateTimer();

    // Polling tự động kiểm tra mỗi 2 giây xem khách đã quét xong trên điện thoại chưa
    pollInterval = setInterval(async () => {
        try {
            const res = await fetch(`{{ url('/zalo-auth/check') }}/${token}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success && data.data && data.data.verified) {
                clearInterval(pollInterval);
                clearInterval(countdownInterval);
                document.getElementById('statusSuccess').classList.remove('hidden');
                setTimeout(() => {
                    window.location.href = data.data.redirect_url || "{{ route('customer.profile') }}";
                }, 1500);
            }
        } catch (err) {
            console.error('Polling check error:', err);
        }
    }, 2000);

    // Hàm giả lập test ngay trên máy tính
    async function mockScanZalo() {
        try {
            const res = await fetch("{{ route('zalo.verify.mock-confirm') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    token: token,
                    name: "{{ $customer->name ?? $verification->name ?? 'phat' }}",
                    phone: "{{ $customer->phone ?? $verification->phone ?? '0974194305' }}"
                })
            });
            const data = await res.json();
            if (data.success) {
                clearInterval(pollInterval);
                clearInterval(countdownInterval);
                document.getElementById('statusSuccess').classList.remove('hidden');
                setTimeout(() => {
                    window.location.href = data.redirect_url || "{{ route('customer.profile') }}";
                }, 1200);
            }
        } catch (e) {
            alert('Lỗi thử nghiệm, vui lòng thử lại.');
        }
    }
</script>
@endsection
