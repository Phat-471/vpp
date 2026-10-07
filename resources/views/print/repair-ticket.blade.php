<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Phiếu Tiếp Nhận Máy In - {{ $ticket->ticket_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; background: #fff; }
            .print-page { box-shadow: none; border: none; margin: 0; width: 100%; max-width: 100%; padding: 10mm; }
        }
        @page {
            size: A4 portrait;
            margin: 8mm;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 font-sans antialiased text-sm">

    <!-- Print Action Bar for Screen -->
    <div class="no-print bg-slate-800 text-white p-3 fixed top-0 left-0 right-0 z-50 flex items-center justify-between shadow-lg">
        <div class="flex items-center space-x-3">
            <span class="font-bold text-amber-400">PHIẾU TIẾP NHẬN SỬA MÁY IN:</span>
            <span class="bg-slate-700 px-2 py-0.5 rounded font-mono">{{ $ticket->ticket_code }}</span>
            <span class="text-xs text-gray-300">Khổ in: A4 / A5 (Máy in văn phòng)</span>
        </div>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 py-1.5 rounded shadow flex items-center space-x-1 cursor-pointer">
                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>BẤM ĐỂ IN (CTRL + P)</span>
            </button>
            <button onclick="window.close()" class="bg-gray-600 hover:bg-gray-500 text-white px-3 py-1.5 rounded">Đóng</button>
        </div>
    </div>

    <div class="max-w-[210mm] mx-auto bg-white p-6 my-14 shadow-md border border-gray-300 print-page">

        <!-- ================= PHẦN 1: CUỐNG DÁN VỎ MÁY IN ================= -->
        <div class="border-2 border-gray-800 rounded-lg p-3 bg-gray-50 mb-4">
            <div class="flex items-center justify-between border-b border-gray-400 pb-2 mb-2">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-red-600">[PHẦN 1: CUỐNG DÁN LÊN THÂN MÁY]</span>
                    <h2 class="text-xl font-extrabold text-gray-900 leading-tight">MÃ PHIẾU: <span class="text-indigo-800 font-mono">{{ $ticket->ticket_code }}</span></h2>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500">Ngày tiếp nhận</div>
                    <div class="font-bold text-sm">{{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                    <p><span class="font-semibold text-gray-700">Khách hàng:</span> <span class="font-bold text-sm">{{ $ticket->customer_name }}</span></p>
                    <p><span class="font-semibold text-gray-700">Điện thoại:</span> <span class="font-bold font-mono">{{ $ticket->customer_phone }}</span></p>
                    <p><span class="font-semibold text-gray-700">Thiết bị:</span> <span class="font-bold text-indigo-700">{{ $ticket->device_name }}</span> (S/N: {{ $ticket->serial_number ?: 'Không' }})</p>
                    <p><span class="font-semibold text-gray-700">Phụ kiện kèm:</span> {{ $ticket->accessories ?: 'Không có' }}</p>
                </div>
                <div class="bg-white p-2 rounded border border-gray-300">
                    <p class="font-semibold text-red-700">Hiện trạng lỗi ghi nhận:</p>
                    <p class="italic text-gray-800">{{ $ticket->issue_description }}</p>
                    <div class="mt-2 pt-1 border-t border-gray-200 flex justify-between items-center text-[11px]">
                        <span>Thợ: <b>{{ $ticket->technician?->name ?: 'Chưa phân công' }}</b></span>
                        <span class="text-red-600 font-bold">Hẹn trả: {{ $ticket->promised_at?->format('H:i d/m') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= ĐƯỜNG CẮT RỜI ================= -->
        <div class="relative my-4 text-center">
            <div class="border-t-2 border-dashed border-gray-400 w-full"></div>
            <span class="bg-white px-3 text-[11px] text-gray-500 font-semibold absolute top-[-9px] left-1/2 -translate-x-1/2">
                ✂ ĐƯỜNG CẮT RỜI — PHẦN TRÊN DÁN MÁY — PHẦN DƯỚI GIAO KHÁCH HÀNG ✂
            </span>
        </div>

        <!-- ================= PHẦN 2: PHIẾU HẸN GIAO KHÁCH HÀNG ================= -->
        <div class="border border-gray-400 rounded-lg p-4 bg-white mt-4">
            <!-- Header Shop -->
            <div class="flex justify-between items-start border-b border-gray-300 pb-3 mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-indigo-600 text-white rounded-lg flex items-center justify-center font-black text-xl shadow">
                        VPP
                    </div>
                    <div>
                        <h1 class="text-lg font-black text-indigo-900 uppercase">CỬA HÀNG VĂN PHÒNG PHẨM & DỊCH VỤ MÁY IN</h1>
                        <p class="text-xs text-gray-600">ĐC: Số 123 Đường Văn Phòng Phẩm, P. Trung Tâm, TP.HCM</p>
                        <p class="text-xs text-gray-600 font-bold">Hotline: <span class="text-red-600">0901.234.567</span> | Zalo: <span class="text-blue-600">0901.234.567</span></p>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-base font-black text-gray-800 uppercase">PHIẾU HẸN TIẾP NHẬN MÁY</h2>
                    <p class="text-xs font-mono font-bold text-indigo-700 text-base">{{ $ticket->ticket_code }}</p>
                    <p class="text-[11px] text-gray-500">Giờ vào: {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <!-- Customer & Device Info -->
            <div class="grid grid-cols-2 gap-4 mb-3 text-xs bg-slate-50 p-2.5 rounded border border-slate-200">
                <div>
                    <p><span class="text-gray-600">Họ tên khách:</span> <b class="text-sm text-gray-900">{{ $ticket->customer_name }}</b></p>
                    <p><span class="text-gray-600">Số điện thoại:</span> <b class="font-mono text-gray-900">{{ $ticket->customer_phone }}</b></p>
                    <p><span class="text-gray-600">Phụ kiện gửi lại:</span> {{ $ticket->accessories ?: 'Không kèm dây' }}</p>
                </div>
                <div>
                    <p><span class="text-gray-600">Tên thiết bị:</span> <b class="text-indigo-800 text-sm">{{ $ticket->device_name }}</b></p>
                    <p><span class="text-gray-600">Tình trạng lỗi:</span> <span class="italic text-gray-800">{{ $ticket->issue_description }}</span></p>
                    <p><span class="text-gray-600">Thời gian hẹn trả:</span> <b class="text-red-600 text-sm font-black">{{ $ticket->promised_at?->format('H:i - ngày d/m/Y') }}</b></p>
                </div>
            </div>

            <!-- Parts & Cost Table (if any) -->
            @if($ticket->repairItems->count() > 0 || $ticket->labor_fee > 0)
            <div class="mb-3">
                <table class="w-full text-xs text-left border-collapse border border-gray-300">
                    <thead class="bg-gray-100 font-bold text-gray-700">
                        <tr>
                            <th class="border border-gray-300 p-1.5 w-8 text-center">STT</th>
                            <th class="border border-gray-300 p-1.5">Hạng mục sửa chữa / Linh kiện thay thế</th>
                            <th class="border border-gray-300 p-1.5 w-12 text-center">SL</th>
                            <th class="border border-gray-300 p-1.5 w-24 text-right">Đơn giá</th>
                            <th class="border border-gray-300 p-1.5 w-28 text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ticket->repairItems as $index => $item)
                        <tr>
                            <td class="border border-gray-300 p-1.5 text-center">{{ $index + 1 }}</td>
                            <td class="border border-gray-300 p-1.5">{{ $item->item_name }}</td>
                            <td class="border border-gray-300 p-1.5 text-center">{{ $item->quantity }}</td>
                            <td class="border border-gray-300 p-1.5 text-right font-mono">{{ number_format($item->unit_price, 0, ',', '.') }} ₫</td>
                            <td class="border border-gray-300 p-1.5 text-right font-mono font-bold">{{ number_format($item->subtotal, 0, ',', '.') }} ₫</td>
                        </tr>
                        @endforeach
                        @if($ticket->labor_fee > 0)
                        <tr>
                            <td class="border border-gray-300 p-1.5 text-center">{{ $ticket->repairItems->count() + 1 }}</td>
                            <td class="border border-gray-300 p-1.5 font-semibold">Tiền công kỹ thuật & Vệ sinh bảo dưỡng máy</td>
                            <td class="border border-gray-300 p-1.5 text-center">1</td>
                            <td class="border border-gray-300 p-1.5 text-right font-mono">{{ number_format($ticket->labor_fee, 0, ',', '.') }} ₫</td>
                            <td class="border border-gray-300 p-1.5 text-right font-mono font-bold">{{ number_format($ticket->labor_fee, 0, ',', '.') }} ₫</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Summary & QR Lookup Block -->
            <div class="grid grid-cols-12 gap-3 items-center border-t border-gray-300 pt-3">
                <!-- QR Tra cứu tiến độ (Chống IDOR: Mã + 4 số cuối SĐT) -->
                <div class="col-span-4 flex items-center space-x-2 bg-indigo-50 p-2 rounded border border-indigo-100">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($lookupUrl) }}" alt="QR Tra cứu" class="w-16 h-16 border border-gray-300 bg-white p-0.5 rounded" />
                    <div class="text-[11px] leading-snug">
                        <span class="font-bold text-indigo-900 block uppercase">QUÉT TRA CỨU TIẾN ĐỘ:</span>
                        <span class="text-gray-600 block">Dùng camera điện thoại quét để xem máy đã sửa xong chưa.</span>
                    </div>
                </div>

                <!-- QR VietQR Thanh toán (nếu chưa trả đủ) -->
                <div class="col-span-4 flex items-center space-x-2 bg-emerald-50 p-2 rounded border border-emerald-100">
                    <img src="{{ $vietQrUrl }}" alt="VietQR" class="w-16 h-16 border border-gray-300 bg-white p-0.5 rounded" />
                    <div class="text-[11px] leading-snug">
                        <span class="font-bold text-emerald-900 block uppercase">VIETQR CHUYỂN KHOẢN:</span>
                        <span class="text-gray-600 block">Quét bằng app ngân hàng tự khớp tiền và mã đơn.</span>
                    </div>
                </div>

                <!-- Totals -->
                <div class="col-span-4 text-right text-xs">
                    <div class="flex justify-between py-0.5">
                        <span class="text-gray-600">Tổng chi phí:</span>
                        <span class="font-bold font-mono text-sm">{{ number_format($ticket->grand_total, 0, ',', '.') }} ₫</span>
                    </div>
                    <div class="flex justify-between py-0.5">
                        <span class="text-gray-600">Đã thanh toán:</span>
                        <span class="font-mono text-emerald-700 font-bold">{{ number_format($ticket->paid_amount, 0, ',', '.') }} ₫</span>
                    </div>
                    <div class="flex justify-between py-0.5 border-t border-gray-300 pt-1">
                        <span class="font-bold text-red-600">Còn lại cần trả:</span>
                        <span class="font-bold font-mono text-red-600 text-base">{{ number_format($ticket->remaining_amount, 0, ',', '.') }} ₫</span>
                    </div>
                </div>
            </div>

            <!-- Notes & Signatures -->
            <div class="mt-4 pt-2 border-t border-gray-200 text-[11px] text-gray-500">
                <p><b>* Lưu ý:</b> Quý khách vui lòng mang theo phiếu này khi đến nhận máy. Bảo hành linh kiện thay thế 03 tháng. Sau 30 ngày kể từ ngày hẹn nếu quý khách không đến nhận, cửa hàng không chịu trách nhiệm bảo quản thiết bị.</p>
                <div class="grid grid-cols-2 text-center mt-3 text-xs">
                    <div>
                        <p class="font-bold text-gray-800">KHÁCH HÀNG KÝ NHẬN</p>
                        <p class="text-[10px] italic text-gray-400">(Ký và ghi rõ họ tên)</p>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">NGƯỜI LẬP PHIẾU</p>
                        <p class="text-[10px] italic text-gray-400">{{ $ticket->creator?->name ?: 'Nhân viên thu ngân' }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
