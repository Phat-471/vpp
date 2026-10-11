<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phiếu Kết Ca POS #{{ $shift->id }} - {{ $shift->cashier?->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/print-engine.css') }}?v={{ filemtime(public_path('css/print-engine.css')) }}">
    <style>
        @page {
            size: 80mm auto;
            margin: 2mm;
        }
        .receipt-border {
            border-top: 1px dashed #94a3b8;
            border-bottom: 1px dashed #94a3b8;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased text-xs">

    <!-- Action Bar hiển thị trên màn hình máy tính -->
    <div class="no-print bg-slate-900 text-white p-3 fixed top-0 left-0 right-0 z-50 flex items-center justify-between shadow-lg">
        <div class="flex items-center space-x-2">
            <span class="font-bold text-amber-400">📊 BIÊN BẢN KẾT CA POS:</span>
            <span class="bg-slate-800 px-2 py-0.5 rounded font-mono font-bold text-white border border-slate-700">Ca #{{ $shift->id }}</span>
            <span class="text-xs text-slate-300">Thu ngân: <b class="text-white">{{ $shift->cashier?->name }}</b></span>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-3.5 py-1.5 rounded-lg shadow-sm flex items-center space-x-1 cursor-pointer transition">
                <span>🖨️ BẤM ĐỂ IN (CTRL + P)</span>
            </button>
            <button onclick="window.close()" class="bg-slate-700 hover:bg-slate-600 text-white px-2.5 py-1.5 rounded-lg text-xs transition">Đóng</button>
        </div>
    </div>

    <!-- Khổ in nhiệt chuẩn K80 (chiều rộng tối đa 76mm-80mm) -->
    <div class="max-w-[76mm] w-[76mm] mx-auto bg-white shadow-md border border-slate-300 p-3 my-14 print-page">
        <!-- Tiêu đề Header -->
        <div class="text-center pb-2 border-b border-slate-200">
            <h1 class="font-black text-sm uppercase tracking-wide">VĂN PHÒNG PHẨM & MÁY IN</h1>
            <p class="text-[10px] text-slate-500">Dịch vụ In ấn - Nạp mực - Sửa chữa máy in</p>
            <p class="text-[10px] text-slate-500">Hotline: 0912.345.678 - 0987.654.321</p>
            <div class="mt-2 pt-1 border-t border-slate-200">
                <h2 class="font-bold text-xs uppercase text-slate-800">PHIẾU BÀN GIAO VÀ KẾT CA</h2>
                <span class="inline-block px-2 py-0.5 mt-1 text-[9px] font-bold rounded {{ $shift->status === \App\Enums\PosShiftStatus::Closed ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                    {{ $shift->status === \App\Enums\PosShiftStatus::Closed ? '✓ ĐÃ CHỐT ĐÓNG CA' : '⏳ CA ĐANG MỞ (TẠM TÍNH)' }}
                </span>
            </div>
        </div>

        <!-- Thông tin ca làm việc -->
        <div class="py-2 space-y-1 text-[11px] border-b border-slate-200">
            <div class="flex justify-between">
                <span class="text-slate-500">Mã ca:</span>
                <span class="font-mono font-bold">#{{ $shift->id }} ({{ substr($shift->uuid, 0, 8) }})</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Thu ngân:</span>
                <span class="font-bold">{{ $shift->cashier?->name ?? '---' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Mở ca:</span>
                <span>{{ $shift->opened_at?->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Đóng ca:</span>
                <span class="font-semibold">{{ $shift->closed_at ? $shift->closed_at->format('d/m/Y H:i') : 'Chưa đóng' }}</span>
            </div>
        </div>

        <!-- Bảng kê tài chính chi tiết -->
        <div class="py-2 space-y-1.5 text-[11px] border-b border-slate-200">
            <div class="font-bold text-[11px] text-slate-700 uppercase mb-1">1. Doanh số trong ca</div>
            
            <div class="flex justify-between">
                <span>Tiền mặt đầu ca:</span>
                <span class="font-semibold">{{ \App\Helpers\AppHelper::formatMoney($shift->opening_cash) }}</span>
            </div>
            
            <div class="flex justify-between">
                <span>(+) Tiền mặt thu được:</span>
                <span class="font-semibold">{{ \App\Helpers\AppHelper::formatMoney($summary['cash_sales'] ?? $shift->cash_sales ?? 0) }}</span>
            </div>

            <div class="flex justify-between">
                <span>(+) Chuyển khoản VietQR:</span>
                <span class="font-semibold">{{ \App\Helpers\AppHelper::formatMoney($summary['transfer_sales'] ?? $shift->transfer_sales ?? 0) }}</span>
            </div>

            <div class="flex justify-between pt-1 border-t border-slate-100 font-bold">
                <span>Tổng doanh thu bán hàng:</span>
                <span class="text-indigo-700">{{ \App\Helpers\AppHelper::formatMoney($summary['total_sales'] ?? $shift->total_sales ?? 0) }}</span>
            </div>

            @if(!empty($summary['late_transfer']) && (float) $summary['late_transfer'] > 0)
            <div class="flex justify-between text-[10px] text-amber-700 bg-amber-50 p-1 rounded">
                <span>CK nhận thêm sau chốt:</span>
                <span class="font-bold">+{{ \App\Helpers\AppHelper::formatMoney($summary['late_transfer']) }}</span>
            </div>
            @endif
        </div>

        <!-- Bảng kiểm đếm tiền mặt & chênh lệch -->
        <div class="py-2 space-y-1.5 text-[11px] border-b border-slate-200 bg-slate-50 p-2 rounded my-1">
            <div class="font-bold text-[11px] text-slate-800 uppercase mb-1">2. Đối soát tiền mặt két</div>

            <div class="flex justify-between">
                <span class="text-slate-600">Tiền mặt lý thuyết:</span>
                <span class="font-bold">{{ \App\Helpers\AppHelper::formatMoney($summary['expected_cash'] ?? $shift->expected_cash ?? 0) }}</span>
            </div>
            <div class="text-[9px] text-slate-400 -mt-1 italic">(Đầu ca + Tiền mặt thu lẻ)</div>

            <div class="flex justify-between pt-1 border-t border-slate-200">
                <span class="font-bold text-slate-800">Tiền thực tế kiểm đếm:</span>
                <span class="font-bold text-slate-900 text-xs">
                    {{ $shift->counted_cash !== null ? \App\Helpers\AppHelper::formatMoney($shift->counted_cash) : 'Chưa chốt' }}
                </span>
            </div>

            @php
                $diff = (float) ($shift->difference ?? 0);
            @endphp
            <div class="flex justify-between items-center pt-1 border-t border-slate-200 font-bold">
                <span>Chênh lệch tiền mặt:</span>
                <span class="{{ $diff == 0 ? 'text-emerald-700' : ($diff > 0 ? 'text-amber-600' : 'text-rose-600') }}">
                    @if($shift->counted_cash === null)
                        Chưa chốt
                    @elseif($diff == 0)
                        0 ₫ (KHỚP TIỀN)
                    @elseif($diff > 0)
                        +{{ \App\Helpers\AppHelper::formatMoney($diff) }} (THỪA)
                    @else
                        {{ \App\Helpers\AppHelper::formatMoney($diff) }} (THIẾU)
                    @endif
                </span>
            </div>

            @if($shift->notes)
            <div class="text-[10px] text-slate-600 pt-1 border-t border-slate-200">
                <span class="font-semibold">Lý do/Ghi chú:</span> {{ $shift->notes }}
            </div>
            @endif
        </div>

        <!-- Thống kê đơn hàng -->
        <div class="py-2 space-y-1 text-[11px] border-b border-slate-200">
            <div class="font-bold text-[11px] text-slate-700 uppercase mb-1">3. Thống kê đơn hàng</div>
            <div class="flex justify-between">
                <span>Tổng số đơn trong ca:</span>
                <span class="font-bold">{{ $summary['order_count'] ?? $shift->order_count ?? 0 }} đơn</span>
            </div>
            <div class="flex justify-between">
                <span>Đơn chuyển khoản chờ duyệt:</span>
                <span class="font-semibold">{{ $summary['pending_transfer_count'] ?? $shift->pending_transfer_count ?? 0 }} đơn</span>
            </div>
        </div>

        <!-- Chữ ký xác nhận bàn giao -->
        <div class="pt-4 pb-2 avoid-break">
            <div class="grid grid-cols-2 gap-2 text-center text-[10px]">
                <div>
                    <p class="font-bold uppercase text-slate-700">Thu ngân giao ca</p>
                    <p class="text-[9px] text-slate-400 italic mb-8">(Ký và ghi rõ họ tên)</p>
                    <p class="font-bold text-slate-800 border-t border-slate-200 pt-1 inline-block min-w-[70px]">{{ $shift->cashier?->name }}</p>
                </div>
                <div>
                    <p class="font-bold uppercase text-slate-700">Quản lý / Nhận ca</p>
                    <p class="text-[9px] text-slate-400 italic mb-8">(Ký và ghi rõ họ tên)</p>
                    <p class="font-bold text-slate-800 border-t border-slate-200 pt-1 inline-block min-w-[70px]">&nbsp;</p>
                </div>
            </div>
        </div>

        <!-- Footer thời gian in -->
        <div class="text-center pt-3 border-t border-dashed border-slate-300 text-[9px] text-slate-400">
            <p>In lúc: {{ now()->format('d/m/Y H:i:s') }}</p>
            <p class="italic">Cảm ơn quý khách và chúc ca làm việc hiệu quả!</p>
        </div>
    </div>

</body>
</html>
