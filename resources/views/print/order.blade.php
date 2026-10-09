<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn Bán Lẻ {{ request('format') === 'k80' ? '(K80)' : '(A5)' }} - {{ $order->order_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; background: #fff; }
            .print-page { box-shadow: none !important; border: none !important; margin: 0 !important; width: 100% !important; padding: 2mm !important; }
        }
        @if(request('format') === 'k80')
        @page {
            size: 80mm auto;
            margin: 2mm;
        }
        @else
        @page {
            size: A5 portrait;
            margin: 6mm;
        }
        @endif
    </style>
</head>
<body class="bg-gray-100 text-gray-900 font-sans antialiased text-sm">

    @php
        $isK80 = request('format') === 'k80';
    @endphp

    <!-- Print Action Bar for Screen -->
    <div class="no-print bg-slate-900 text-white p-3 fixed top-0 left-0 right-0 z-50 flex items-center justify-between shadow-lg">
        <div class="flex items-center space-x-3">
            <span class="font-bold text-emerald-400">🧾 HÓA ĐƠN BÁN LẺ:</span>
            <span class="bg-slate-700 px-2 py-0.5 rounded font-mono font-bold">{{ $order->order_code }}</span>
            <span class="text-xs text-gray-300">Khổ in: <b class="text-amber-400">{{ $isK80 ? 'Máy in nhiệt K80 (80mm)' : 'Máy in văn phòng A5 / A4' }}</b></span>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ request()->fullUrlWithQuery(['format' => null]) }}" class="px-3 py-1.5 rounded font-bold text-xs transition {{ !$isK80 ? 'bg-indigo-600 text-white shadow' : 'bg-slate-800 text-gray-300 hover:bg-slate-700' }}">
                📄 Khổ A5 (Mặc định)
            </a>
            <a href="{{ request()->fullUrlWithQuery(['format' => 'k80']) }}" class="px-3 py-1.5 rounded font-bold text-xs transition {{ $isK80 ? 'bg-indigo-600 text-white shadow' : 'bg-slate-800 text-gray-300 hover:bg-slate-700' }}">
                🧾 Khổ K80 (Nhiệt 80mm)
            </a>
            @if($order->is_vat_invoice)
            <a href="{{ url("/print/vat-invoice/{$order->id}") }}" class="px-3 py-1.5 rounded font-bold text-xs bg-amber-600 hover:bg-amber-500 text-white transition">
                📄 Xem Hóa đơn VAT
            </a>
            @endif
            <button onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 py-1.5 rounded shadow flex items-center space-x-1 cursor-pointer">
                <span>BẤM ĐỂ IN (CTRL + P)</span>
            </button>
            <button onclick="window.close()" class="bg-gray-600 hover:bg-gray-500 text-white px-3 py-1.5 rounded text-xs">Đóng</button>
        </div>
    </div>

    <div class="{{ $isK80 ? 'max-w-[76mm] w-[76mm] p-2 text-xs' : 'max-w-[148mm] p-6 text-sm' }} mx-auto bg-white my-14 shadow-md border border-gray-300 print-page">
        <!-- Shop Header -->
        <div class="text-center border-b border-gray-300 pb-3 mb-3">
            <h1 class="text-base font-black uppercase text-indigo-950">{{ $storefrontSettings['site_name'] }}</h1>
            @if($storefrontSettings['address'])<p class="text-xs text-gray-600">Địa chỉ: {{ $storefrontSettings['address'] }}</p>@endif
            @if($storefrontSettings['hotline'])<p class="text-xs text-gray-600">Hotline: <b>{{ $storefrontSettings['hotline'] }}</b></p>@endif
            @if($storefrontSettings['zalo'])<p class="text-xs text-gray-600">Zalo: {{ $storefrontSettings['zalo'] }}</p>@endif
            @if($storefrontSettings['email'])<p class="text-xs text-gray-600">Email: {{ $storefrontSettings['email'] }}</p>@endif
            <h2 class="text-lg font-black uppercase text-gray-800 mt-2">HÓA ĐƠN BÁN LẺ</h2>
            <div class="text-xs text-gray-500 flex justify-between px-2 mt-1">
                <span>Số HĐ: <b class="font-mono text-gray-900">{{ $order->order_code }}</b></span>
                <span>Ngày: {{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="text-xs text-gray-700 text-left px-2 mt-1">
                <p>Khách hàng: <b>{{ $order->customer_name ?: 'Khách lẻ vãng lai' }}</b> {{ $order->customer_phone ? "({$order->customer_phone})" : '' }}</p>
                <p>Thu ngân: {{ $order->creator?->name ?: 'Thu ngân tại quầy' }}</p>
            </div>
        </div>

        @if($order->is_vat_invoice)
            <div class="text-xs border-b border-gray-300 pb-3 mb-3">
                <p><b>Thông tin yêu cầu xuất hóa đơn</b></p>
                <p>Mã số thuế: {{ $order->company_tax_id }}</p>
                <p>Tên doanh nghiệp: {{ $order->company_name }}</p>
                <p>Địa chỉ: {{ $order->company_address }}</p>
                <p>Email nhận hóa đơn: {{ $order->invoice_email }}</p>
                <p class="text-gray-500">Phiếu bán hàng này không thay thế hóa đơn điện tử.</p>
            </div>
        @endif
        <!-- Order Items Display -->
        @if($isK80)
        <!-- K80 Receipt Items (Chuẩn máy in nhiệt 80mm) -->
        <div class="border-t border-b border-dashed border-gray-400 py-1.5 my-2">
            <div class="flex justify-between font-bold text-[11px] text-gray-700 pb-1 border-b border-gray-200">
                <span>MẶT HÀNG</span>
                <span>T.TIỀN</span>
            </div>
            @foreach($order->orderItems as $index => $item)
            <div class="py-1 border-b border-gray-100 last:border-0 text-[11px]">
                <div class="font-semibold text-gray-900 leading-tight">{{ $item->product_name }}</div>
                <div class="flex justify-between text-[10px] text-gray-600 font-mono mt-0.5">
                    <span>{{ $item->quantity }} {{ $item->unit_name }} x {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                    <span class="font-bold text-gray-900">{{ number_format($item->subtotal, 0, ',', '.') }} ₫</span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Order Items Table A5 -->
        <table class="w-full text-xs text-left border-collapse border border-gray-300 mb-3">
            <thead class="bg-gray-100 font-bold text-gray-700">
                <tr>
                    <th class="border border-gray-300 p-1.5 text-center w-7">#</th>
                    <th class="border border-gray-300 p-1.5">Tên hàng hóa</th>
                    <th class="border border-gray-300 p-1.5 text-center w-10">ĐVT</th>
                    <th class="border border-gray-300 p-1.5 text-center w-8">SL</th>
                    <th class="border border-gray-300 p-1.5 text-right w-20">Đơn giá</th>
                    <th class="border border-gray-300 p-1.5 text-right w-24">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderItems as $index => $item)
                <tr>
                    <td class="border border-gray-300 p-1.5 text-center">{{ $index + 1 }}</td>
                    <td class="border border-gray-300 p-1.5 font-medium">{{ $item->product_name }}</td>
                    <td class="border border-gray-300 p-1.5 text-center">{{ $item->unit_name }}</td>
                    <td class="border border-gray-300 p-1.5 text-center font-bold">{{ $item->quantity }}</td>
                    <td class="border border-gray-300 p-1.5 text-right font-mono">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="border border-gray-300 p-1.5 text-right font-mono font-bold">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Totals & VAT Breakdown -->
        <div class="text-xs border-t border-gray-300 pt-2 space-y-1">
            <div class="flex justify-between">
                <span class="text-gray-600">Tiền hàng:</span>
                <span class="font-mono font-semibold">{{ number_format($order->subtotal, 0, ',', '.') }} ₫</span>
            </div>
            @if($order->discount_amount > 0)
            <div class="flex justify-between text-amber-700">
                <span>Chiết khấu / Giảm giá:</span>
                <span class="font-mono">-{{ number_format($order->discount_amount, 0, ',', '.') }} ₫</span>
            </div>
            @endif
            @if($order->tax_amount > 0)
            <div class="flex justify-between text-gray-500 text-[11px]">
                <span>Thuế suất VAT ({{ $order->tax_rate }}%):</span>
                <span class="font-mono">{{ number_format($order->tax_amount, 0, ',', '.') }} ₫</span>
            </div>
            @endif
            <div class="flex justify-between items-baseline text-sm font-black border-t border-gray-400 pt-1 text-gray-900">
                <span class="{{ $isK80 ? 'text-xs' : 'text-sm' }} uppercase">TỔNG CỘNG:</span>
                <span class="{{ $isK80 ? 'text-sm' : 'text-base' }} text-red-600 font-mono font-bold">{{ number_format($order->grand_total, 0, ',', '.') }} ₫</span>
            </div>
            <div class="flex justify-between text-[11px] text-gray-600">
                <span>Hình thức: {{ $order->payment_method === 'vietqr' ? 'VietQR Động' : ($order->payment_method === 'transfer' ? 'Chuyển khoản' : 'Tiền mặt') }}</span>
                <span class="font-bold text-emerald-700">{{ $order->payment_status === 'paid' ? 'ĐÃ THANH TOÁN ĐỦ' : 'CHƯA THANH TOÁN' }}</span>
            </div>
            @if($order->payment_method === 'cash')
                @if($order->cash_received !== null)
                    <div class="flex justify-between"><span>Tiền khách đưa:</span><span class="font-mono font-semibold">{{ number_format($order->cash_received, 0, ',', '.') }} đ</span></div>
                    <div class="flex justify-between"><span>Tiền thừa trả khách:</span><span class="font-mono font-semibold">{{ number_format($paymentSummary['change'], 0, ',', '.') }} đ</span></div>
                @else
                    <p class="text-gray-500">Đơn cũ chưa lưu thông tin tiền khách đưa và tiền thừa.</p>
                @endif
            @endif
        </div>

        <!-- VietQR Box if not paid yet -->
        @if($order->payment_status !== 'paid' && ($vietQrUrl ?? null))
        <div class="mt-3 p-2 bg-emerald-50 rounded border border-emerald-200 flex items-center space-x-3">
            <img src="{{ $vietQrUrl }}" alt="VietQR" class="w-16 h-16 border border-emerald-300 rounded bg-white p-0.5" />
            <div class="text-[11px]">
                <b class="text-emerald-900 uppercase block">QUÉT VIETQR ĐỂ TRẢ TIỀN:</b>
                <span class="text-gray-600 block">Tự động nhận diện thanh toán không cần chụp màn hình chuyển khoản.</span>
            </div>
        </div>
        @endif

        <!-- Footer greeting -->
        <div class="mt-4 text-center text-xs text-gray-500 border-t border-gray-200 pt-3">
            <p class="font-semibold text-gray-800">Cảm ơn Quý khách & Hẹn gặp lại!</p>
            <p class="text-[10px] italic">Quý khách vui lòng kiểm tra hàng hóa trước khi rời quầy.</p>
        </div>
    </div>

    @if(request()->routeIs('pos.receipt') && request()->boolean('autoprint'))
        <script src="{{ asset('js/pos-receipt.js') }}?v={{ filemtime(public_path('js/pos-receipt.js')) }}" defer></script>
    @endif
</body>
</html>
