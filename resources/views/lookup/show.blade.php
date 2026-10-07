<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiến Độ Sửa Máy In - {{ $ticket->ticket_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen flex flex-col justify-between antialiased">

    <!-- Header Navigation Bar -->
    <header class="bg-indigo-950 text-white shadow-md sticky top-0 z-40 border-b border-indigo-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('lookup.index') }}" class="text-xs bg-indigo-900 hover:bg-indigo-800 text-indigo-200 px-3 py-1.5 rounded-xl border border-indigo-800 flex items-center space-x-1">
                    <span>← Tra cứu phiếu khác</span>
                </a>
                <span class="text-slate-500 hidden sm:inline">|</span>
                <span class="font-mono font-bold text-amber-300 text-sm hidden sm:inline">MÃ PHIẾU: {{ $ticket->ticket_code }}</span>
            </div>

            <div class="flex items-center space-x-3">
                <span class="text-xs text-slate-300 hidden md:inline">Ngày nhận: {{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                <a href="{{ url('/') }}" class="text-xs bg-white text-indigo-950 font-bold px-3 py-1.5 rounded-xl hover:bg-amber-400 transition">
                    Trang chủ VPP
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Grid (Multi-Device Responsive: 12 Cols on Desktop, 1 Col on Mobile) -->
    <main class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex-1">
        
        <!-- Top Status Summary Pill -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200 gap-3">
            <div>
                <span class="text-xs uppercase tracking-wider font-bold text-slate-400 block">Tiến độ phiếu tiếp nhận</span>
                <div class="flex items-center space-x-2 mt-0.5">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-mono">{{ $ticket->ticket_code }}</h1>
                    <span class="text-slate-400 text-sm">•</span>
                    <span class="text-sm sm:text-base font-bold text-indigo-700">{{ $ticket->device_name }}</span>
                </div>
            </div>
            <div>
                @if($ticket->status === 'received')
                    <span class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-800 text-xs sm:text-sm font-black rounded-xl">
                        1. ĐÃ TIẾP NHẬN MÁY TẠI QUẦY
                    </span>
                @elseif($ticket->status === 'diagnosing')
                    <span class="inline-flex items-center px-4 py-2 bg-amber-100 text-amber-900 text-xs sm:text-sm font-black rounded-xl animate-pulse">
                        2. ĐANG KIỂM TRA & THÁO MÁY
                    </span>
                @elseif($ticket->status === 'waiting_approval')
                    <span class="inline-flex items-center px-4 py-2 bg-sky-100 text-sky-900 text-xs sm:text-sm font-black rounded-xl">
                        3. CHỜ QUÝ KHÁCH DUYỆT GIÁ
                    </span>
                @elseif($ticket->status === 'in_progress')
                    <span class="inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-900 text-xs sm:text-sm font-black rounded-xl animate-pulse">
                        4. ĐANG TIẾN HÀNH SỬA CHỮA
                    </span>
                @elseif($ticket->status === 'completed')
                    <span class="inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-900 text-xs sm:text-sm font-black rounded-xl">
                        5. ĐÃ SỬA XONG — SẴN SÀNG NHẬN MÁY
                    </span>
                @elseif($ticket->status === 'delivered')
                    <span class="inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-900 text-xs sm:text-sm font-black rounded-xl">
                        6. ĐÃ BÀN GIAO THIẾT BỊ
                    </span>
                @else
                    <span class="inline-flex items-center px-4 py-2 bg-rose-100 text-rose-900 text-xs sm:text-sm font-black rounded-xl">
                        ĐÃ HỦY
                    </span>
                @endif
            </div>
        </div>

        <!-- 2-Column Responsive Grid on Desktop / Tablet (12 Columns) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT COLUMN: Stepper Timeline & Device Details (Desktop: 7 cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Visual Stepper Timeline Box -->
                <div class="bg-white p-6 sm:p-7 rounded-3xl shadow-sm border border-slate-200">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 mb-5">
                        Các Giai Đoạn Xử Lý Máy In
                    </h2>

                    <!-- Stepper Grid (4 Steps) -->
                    <div class="grid grid-cols-4 gap-2 text-center text-xs font-bold">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-sm shadow-sm {{ in_array($ticket->status, ['received', 'diagnosing', 'waiting_approval', 'in_progress', 'completed', 'delivered']) ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400' }}">
                                ✓
                            </div>
                            <span class="mt-2 text-slate-800">1. Tiếp nhận</span>
                            <span class="text-[10px] font-normal text-slate-400">{{ $ticket->created_at->format('d/m') }}</span>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-sm shadow-sm {{ in_array($ticket->status, ['diagnosing', 'waiting_approval', 'in_progress', 'completed', 'delivered']) ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400' }}">
                                {{ in_array($ticket->status, ['in_progress', 'completed', 'delivered']) ? '✓' : '2' }}
                            </div>
                            <span class="mt-2 text-slate-800">2. Kiểm tra</span>
                            <span class="text-[10px] font-normal text-slate-400">Tháo máy</span>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-sm shadow-sm {{ in_array($ticket->status, ['in_progress', 'completed', 'delivered']) ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400' }}">
                                {{ in_array($ticket->status, ['completed', 'delivered']) ? '✓' : '3' }}
                            </div>
                            <span class="mt-2 text-slate-800">3. Sửa chữa</span>
                            <span class="text-[10px] font-normal text-slate-400">Thay linh kiện</span>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-sm shadow-sm {{ in_array($ticket->status, ['completed', 'delivered']) ? 'bg-emerald-600 text-white font-black' : 'bg-slate-100 text-slate-400' }}">
                                {{ $ticket->status === 'delivered' ? '✓' : '4' }}
                            </div>
                            <span class="mt-2 text-emerald-700 font-black">4. Xong máy</span>
                            <span class="text-[10px] font-normal text-slate-400">Sẵn sàng nhận</span>
                        </div>
                    </div>
                </div>

                <!-- Device & Technician Diagnostics Box -->
                <div class="bg-white p-6 sm:p-7 rounded-3xl shadow-sm border border-slate-200 space-y-4 text-xs sm:text-sm">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-3 flex items-center justify-between">
                        <span>Chi Tiết Máy & Hiện Trạng Ghi Nhận</span>
                        <span class="font-mono text-indigo-700 text-xs">{{ $ticket->device_name }}</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <span class="text-slate-400 block text-xs">Khách hàng đăng ký:</span>
                            <span class="font-extrabold text-slate-900">{{ $ticket->customer_name }}</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-slate-400 block text-xs">Phụ kiện kèm theo khi nhận:</span>
                            <span class="font-semibold text-slate-800">{{ $ticket->accessories ?: 'Máy trần (không kèm dây)' }}</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-slate-400 block text-xs">Số Serial / Tem máy:</span>
                            <span class="font-mono text-slate-800">{{ $ticket->serial_number ?: 'Không ghi nhận' }}</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-slate-400 block text-xs">Thời gian hẹn trả máy:</span>
                            <span class="font-black text-red-600">{{ $ticket->promised_at?->format('H:i - ngày d/m/Y') }}</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <span class="text-slate-500 font-bold block mb-1">Hiện trạng lỗi khách báo lúc gửi:</span>
                        <p class="bg-slate-50 p-3 rounded-2xl text-slate-800 italic border border-slate-200/80 leading-relaxed">
                            "{{ $ticket->issue_description }}"
                        </p>
                    </div>

                    @if($ticket->technician_diagnosis)
                    <div class="pt-2">
                        <span class="text-indigo-900 font-bold block mb-1">Chẩn đoán kỹ thuật viên:</span>
                        <p class="bg-indigo-50/70 p-3 rounded-2xl text-indigo-950 border border-indigo-100 leading-relaxed">
                            {{ $ticket->technician_diagnosis }}
                        </p>
                    </div>
                    @endif
                </div>

            </div>

            <!-- RIGHT COLUMN: Cost Breakdown & Dynamic VietQR (Desktop: 5 cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Cost & Parts Table Box -->
                @if($ticket->repairItems->count() > 0 || $ticket->labor_fee > 0)
                <div class="bg-white p-6 sm:p-7 rounded-3xl shadow-sm border border-slate-200 text-xs sm:text-sm">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-3 mb-3">
                        Bảng Kê Chi Phí & Linh Kiện
                    </h2>

                    <div class="space-y-3">
                        @foreach($ticket->repairItems as $item)
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                            <div>
                                <span class="font-bold text-slate-900 block">{{ $item->item_name }}</span>
                                <span class="text-xs text-slate-400">{{ $item->quantity }} {{ $item->unit }} x {{ number_format($item->unit_price, 0, ',', '.') }} ₫</span>
                            </div>
                            <span class="font-mono font-bold text-slate-900">{{ number_format($item->subtotal, 0, ',', '.') }} ₫</span>
                        </div>
                        @endforeach

                        @if($ticket->labor_fee > 0)
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                            <div>
                                <span class="font-bold text-slate-900 block">Tiền công kỹ thuật & vệ sinh bảo dưỡng</span>
                                <span class="text-xs text-slate-400">Công tháo lắp và căn chỉnh máy</span>
                            </div>
                            <span class="font-mono font-bold text-slate-900">{{ number_format($ticket->labor_fee, 0, ',', '.') }} ₫</span>
                        </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-200 space-y-1.5 text-xs sm:text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span>Tổng chi phí:</span>
                            <span class="font-bold font-mono text-slate-900">{{ number_format($ticket->grand_total, 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between text-emerald-700">
                            <span>Đã thanh toán:</span>
                            <span class="font-mono font-bold">{{ number_format($ticket->paid_amount, 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between text-base font-black pt-2 border-t border-slate-200">
                            <span class="text-red-600">Còn lại cần trả:</span>
                            <span class="text-red-600 font-mono text-lg">{{ number_format($ticket->remaining_amount, 0, ',', '.') }} ₫</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Dynamic VietQR Payment Box -->
                @if($ticket->remaining_amount > 0)
                <div class="bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-900 text-white p-6 rounded-3xl shadow-lg border border-indigo-800 text-center">
                    <span class="text-xs uppercase tracking-wider text-amber-300 font-extrabold block mb-1">
                        QUÉT VIETQR THANH TOÁN TRỰC TIẾP
                    </span>
                    <p class="text-xs text-slate-300 mb-4">
                        Dùng App ngân hàng quét mã dưới đây để chuyển khoản chính xác, hệ thống tự động cập nhật ngay:
                    </p>

                    <div class="inline-block bg-white p-3 rounded-2xl shadow-xl my-1 border border-white">
                        <img src="{{ $vietQrUrl }}" alt="VietQR" class="w-52 h-52 object-contain mx-auto" />
                    </div>

                    <div class="mt-3 text-xs sm:text-sm">
                        <span class="text-slate-300">Số tiền:</span> 
                        <span class="font-black text-lg text-amber-400 font-mono">{{ number_format($ticket->remaining_amount, 0, ',', '.') }} ₫</span>
                    </div>

                    <div class="text-xs text-slate-300 mt-1">
                        Nội dung chuyển khoản: <code class="bg-white/20 text-white px-2 py-0.5 rounded font-mono font-bold">{{ $ticket->ticket_code }}</code>
                    </div>

                    <p class="text-[11px] text-slate-400 mt-3 italic">
                        * Tự động khớp lệnh qua cổng SePay & Casso chỉ sau 3–5 giây.
                    </p>
                </div>
                @else
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-900 p-6 rounded-3xl text-center shadow-sm">
                    <span class="text-3xl block mb-2">🎉</span>
                    <h3 class="text-base font-black">PHIẾU ĐÃ HOÀN TẤT THANH TOÁN</h3>
                    <p class="text-xs text-emerald-700 mt-1">Cảm ơn Quý khách! Khi đến nhận máy vui lòng mang theo số điện thoại đã đăng ký.</p>
                </div>
                @endif

            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 text-xs py-8 border-t border-slate-800 text-center space-y-2">
        <div class="max-w-7xl mx-auto px-4">
            <p>© 2026 Cửa Hàng Văn Phòng Phẩm & Dịch Vụ Máy In. Bảo mật chống IDOR 100%.</p>
            <div class="flex justify-center space-x-3 pt-2 text-[11px] text-slate-400">
                <a href="{{ url('/') }}" class="hover:text-white transition">Trang Chủ</a>
                <span>•</span>
                <a href="{{ route('storefront.about') }}" class="hover:text-white transition">Giới Thiệu</a>
                <span>•</span>
                <a href="{{ route('storefront.terms') }}" class="hover:text-white transition">Chính Sách Mua Hàng</a>
                <span>•</span>
                <a href="{{ route('storefront.privacy') }}" class="hover:text-white transition font-bold text-emerald-400">Bảo Mật Dữ Liệu</a>
            </div>
        </div>
    </footer>

    <!-- Floating Hotline & Zalo (Bottom-Left) -->
    <div class="fixed bottom-6 left-6 z-40 flex flex-col space-y-2">
        <a href="https://zalo.me/0901234567" target="_blank" class="w-12 h-12 bg-blue-600 hover:bg-blue-500 text-white rounded-full flex items-center justify-center shadow-lg font-bold text-xs transform hover:scale-110 transition border-2 border-white">
            Zalo
        </a>
        <a href="tel:0901234567" class="w-12 h-12 bg-red-600 hover:bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg transform hover:scale-110 transition animate-pulse border-2 border-white">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.45.57 3.57a1 1 0 01-.25 1.02l-2.2 2.2z"></path></svg>
        </a>
    </div>

    <!-- Live Chat Widget -->
    @include('storefront.components.livechat')

</body>
</html>
