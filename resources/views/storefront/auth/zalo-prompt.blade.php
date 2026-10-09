<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Xác Thực Tài Khoản Qua Zalo | VPP & Dịch Vụ Máy In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b1329; color: #f8fafc; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#080d1e] via-[#0d1633] to-[#041a2f] flex flex-col items-center justify-center p-4 antialiased">

    <div class="w-full max-w-sm bg-[#0c1633] border border-blue-500/30 rounded-3xl p-6 shadow-2xl relative overflow-hidden text-center">

        <!-- Top Header Brand -->
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-white font-black text-2xl mx-auto mb-3 shadow-lg shadow-emerald-500/20">
            V
        </div>
        <div class="inline-flex items-center space-x-1.5 px-3 py-0.5 rounded-full bg-blue-500/20 text-blue-400 text-[10px] font-extrabold uppercase tracking-wider mb-1">
            <span>ỨNG DỤNG XÁC THỰC ZALO</span>
        </div>
        <h1 class="text-base font-black text-white">{{ $storefrontSettings['site_name'] ?? 'VPP & Dịch Vụ Máy In' }}</h1>
        <p class="text-xs text-slate-300 mt-1">
            Yêu cầu cấp quyền thông tin để xác thực tài khoản và hỗ trợ giao hàng / sửa máy:
        </p>

        @if($isExpired)
            <!-- Trạng thái đã hết hạn -->
            <div class="my-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
                <div class="font-bold text-sm mb-1">Mã QR Đã Hết Hạn!</div>
                <p>Vui lòng quay lại màn hình máy tính để tải lại mã QR mới.</p>
            </div>
        @elseif($isVerified)
            <!-- Trạng thái đã xác thực trước đó -->
            <div class="my-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs">
                <div class="text-3xl mb-2">✓</div>
                <div class="font-bold text-sm mb-1">Đã Xác Thực Thành Công!</div>
                <p>Màn hình máy tính của bạn đã được đăng nhập.</p>
            </div>
        @else
            <!-- FORM XÁC THỰC CHÍNH (1 CHẠM HOẶC CHỈNH TÊN/SĐT NẾU CẦN) -->
            <div id="confirmBox" class="my-5 bg-[#081026] rounded-2xl p-4 border border-slate-800 text-left space-y-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Tên hiển thị Zalo của bạn:</label>
                    <input type="text" id="zaloName" value="Khách Hàng Zalo" class="w-full text-xs px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white font-semibold focus:outline-none focus:border-blue-500" />
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 mb-1">Số điện thoại đăng ký Zalo:</label>
                    <input type="tel" id="zaloPhone" placeholder="VD: 0988765432" class="w-full text-xs px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-emerald-400 font-mono font-bold focus:outline-none focus:border-blue-500" />
                    <span class="text-[10px] text-slate-500 mt-1 block">Khách hàng xác thực 1 chạm với số điện thoại này</span>
                </div>
            </div>

            <!-- Nút bấm Chia Sẻ -->
            <div id="actionButtons" class="space-y-2">
                <button type="button" onclick="submitZaloVerify()" id="btnSubmit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white font-black text-xs shadow-lg shadow-blue-600/30 transition transform active:scale-95 flex items-center justify-center space-x-2">
                    <span>✓ Cho Phép & Chia Sẻ Thông Tin</span>
                </button>
            </div>

            <!-- Màn hình thành công sau khi bấm -->
            <div id="successBox" class="hidden my-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs">
                <div class="text-3xl mb-2 text-emerald-400">✓</div>
                <div class="font-bold text-sm mb-1 text-white">XÁC THỰC THÀNH CÔNG!</div>
                <p class="text-slate-300">Thông tin Tên & SĐT đã được lưu vào hệ thống.</p>
                <p class="text-emerald-400 font-bold mt-2">Màn hình máy tính của bạn đã tự động đăng nhập!</p>
            </div>
        @endif

        <div class="mt-4 pt-3 border-t border-slate-800 text-[10px] text-slate-500 flex items-center justify-center space-x-1">
            <span>🔒 Bảo mật bởi Zalo • 1 chạm an toàn</span>
        </div>

    </div>

    <script>
        const token = "{{ $verification->token }}";

        async function submitZaloVerify() {
            const name = document.getElementById('zaloName').value.trim();
            const phone = document.getElementById('zaloPhone').value.trim();
            const btn = document.getElementById('btnSubmit');

            if (!phone) {
                alert('Vui lòng nhập số điện thoại Zalo của bạn.');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = 'Đang xử lý...';

            try {
                const res = await fetch("{{ route('zalo.verify.confirm') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        token: token,
                        name: name || 'Khách Zalo',
                        phone: phone,
                        zalo_id: 'zalo_' + phone
                    })
                });

                const data = await res.json();
                if (data.success) {
                    document.getElementById('confirmBox').classList.add('hidden');
                    document.getElementById('actionButtons').classList.add('hidden');
                    document.getElementById('successBox').classList.remove('hidden');
                } else {
                    alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                    btn.disabled = false;
                    btn.innerHTML = '✓ Cho Phép & Chia Sẻ Thông Tin';
                }
            } catch (err) {
                alert('Không thể kết nối tới máy chủ. Vui lòng thử lại!');
                btn.disabled = false;
                btn.innerHTML = '✓ Cho Phép & Chia Sẻ Thông Tin';
            }
        }
    </script>

</body>
</html>
