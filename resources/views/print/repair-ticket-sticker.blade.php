<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Tem Dán Máy In - {{ $ticket->ticket_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; background: #fff; }
            .sticker-container {
                box-shadow: none !important;
                border: 2px solid #000 !important;
                margin: 0 !important;
                page-break-inside: avoid;
            }
        }
        @page {
            size: 100mm 75mm;
            margin: 3mm;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 font-sans antialiased text-xs">

    <!-- Top Action Bar for Screen -->
    <div class="no-print bg-slate-900 text-white p-3 fixed top-0 left-0 right-0 z-50 flex items-center justify-between shadow-lg">
        <div class="flex items-center space-x-3">
            <span class="font-bold text-amber-400">🏷️ TEM DÁN THÂN MÁY IN:</span>
            <span class="bg-slate-700 px-2 py-0.5 rounded font-mono font-bold">{{ $ticket->ticket_code }}</span>
            <span class="text-xs text-gray-300">Khổ: Decal 100x75mm hoặc in nhiệt dán vỏ máy</span>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('print.repair-ticket', $ticket->id) }}" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-3 py-1.5 rounded transition">
                🖨️ Xem Phiếu Tiếp Nhận A4/A5
            </a>
            <button onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 py-1.5 rounded shadow flex items-center space-x-1 cursor-pointer">
                <span>BẤM ĐỂ IN TEM (CTRL + P)</span>
            </button>
            <button onclick="window.close()" class="bg-gray-600 hover:bg-gray-500 text-white px-3 py-1.5 rounded">Đóng</button>
        </div>
    </div>

    <!-- Sticker Container: chuẩn kích thước decal 100mm x 75mm -->
    <div class="w-[98mm] max-w-[98mm] mx-auto bg-white p-3 my-16 shadow-lg border-2 border-slate-900 rounded-lg sticker-container">
        
        <!-- Header: Shop & Code -->
        <div class="flex items-center justify-between border-b-2 border-slate-900 pb-1.5 mb-1.5">
            <div>
                <span class="text-[10px] font-black uppercase text-indigo-900 block leading-tight">
                    {{ $storefrontSettings['site_name'] ?? 'VPP & THIẾT BỊ MÁY IN' }}
                </span>
                <span class="text-[9px] text-slate-500">Hotline: {{ $storefrontSettings['hotline'] ?? '0974.194.305' }}</span>
            </div>
            <div class="text-right">
                <span class="text-[9px] font-bold text-slate-500 block uppercase">MÃ TIẾP NHẬN</span>
                <span class="text-base font-black font-mono text-indigo-700 leading-none block">
                    {{ $ticket->ticket_code }}
                </span>
            </div>
        </div>

        <!-- Body: Left details, Right QR Code -->
        <div class="grid grid-cols-12 gap-2 items-center">
            <div class="col-span-8 space-y-1">
                <div>
                    <span class="text-[10px] text-slate-500 font-semibold">Khách:</span>
                    <b class="text-xs text-slate-900">{{ $ticket->customer_name }}</b>
                    <span class="text-[10px] font-mono font-bold text-indigo-700 block">📞 {{ $ticket->customer_phone }}</span>
                </div>

                <div>
                    <span class="text-[10px] text-slate-500 font-semibold">Thiết bị:</span>
                    <b class="text-xs text-indigo-900 block leading-tight">{{ $ticket->device_name }}</b>
                    @if($ticket->serial_number)
                        <span class="text-[9px] text-slate-500 font-mono">S/N: {{ $ticket->serial_number }}</span>
                    @endif
                </div>

                <div class="text-[10px] leading-tight">
                    <span class="text-slate-500">Phụ kiện:</span>
                    <span class="font-bold">{{ $ticket->accessories ?: 'Không có' }}</span>
                </div>
            </div>

            <!-- QR Tra cứu tiến độ (Chống IDOR: Mã phiếu + 4 số cuối SĐT) -->
            <div class="col-span-4 text-center">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={{ urlencode($lookupUrl) }}" alt="QR Tra cứu" class="w-18 h-18 mx-auto border border-slate-300 p-0.5 rounded bg-white shadow-2xs" />
                <span class="text-[8px] font-bold text-slate-600 block mt-0.5 uppercase tracking-tighter">
                    Quét xem tiến độ
                </span>
            </div>
        </div>

        <!-- Issue Description Box -->
        <div class="mt-1.5 p-1.5 bg-slate-50 rounded border border-slate-300 text-[10px] leading-snug">
            <span class="font-bold text-red-600">Lỗi tiếp nhận:</span>
            <span class="italic text-slate-800">{{ Str::limit($ticket->issue_description, 80) }}</span>
        </div>

        <!-- Footer strip -->
        <div class="mt-1.5 pt-1 border-t border-slate-300 flex items-center justify-between text-[9px] text-slate-600 font-semibold">
            <span>Thợ: <b>{{ $ticket->technician?->name ?: 'Chưa phân công' }}</b></span>
            <span>Nhận: {{ $ticket->created_at->format('d/m H:i') }}</span>
            <span class="text-red-600 font-bold">Hẹn: {{ $ticket->promised_at?->format('H:i d/m') }}</span>
        </div>
    </div>

</body>
</html>
