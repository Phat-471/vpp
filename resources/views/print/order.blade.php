<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $format = request('format');
        $isK80 = $format === 'k80';
        $isA4Dual = $format === 'a4_dual';
        $formatTitle = $isK80 ? '(K80 Nhiệt)' : ($isA4Dual ? '(A4 - 2 Liên)' : '(A5)');
    @endphp
    <title>Hóa Đơn Bán Lẻ {{ $formatTitle }} - {{ $order->order_code }}</title>
    <link rel="stylesheet" href="{{ asset('css/print-engine.css') }}?v={{ filemtime(public_path('css/print-engine.css')) }}">
    <style>
        @if($isK80)
        @page {
            size: 80mm auto;
            margin: 2mm;
        }
        @elseif($isA4Dual)
        @page {
            size: A4 portrait;
            margin: 6mm 8mm;
        }
        @else
        @page {
            size: A5 portrait;
            margin: 5mm;
        }
        @endif
        .cut-line {
            position: relative;
            text-align: center;
            margin: 12px 0;
        }
        .cut-line::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            border-top: 2px dashed #94a3b8;
        }
        .cut-line-text {
            position: relative;
            display: inline-block;
            background: #fff;
            padding: 0 10px;
            font-size: 10px;
            color: #64748b;
            font-weight: 700;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased text-xs">

    <!-- Print Action Bar for Screen -->
    <div class="no-print bg-slate-900 text-white p-3 fixed top-0 left-0 right-0 z-50 flex flex-wrap items-center justify-between gap-2 shadow-lg">
        <div class="flex items-center space-x-2.5">
            <span class="font-bold text-emerald-400">🧾 HÓA ĐƠN BÁN LẺ:</span>
            <span class="bg-slate-800 px-2 py-0.5 rounded font-mono font-bold text-white border border-slate-700">{{ $order->order_code }}</span>
            <span class="text-xs text-slate-300">Khổ in: <b class="text-amber-400">{{ $isK80 ? 'Máy in nhiệt K80 (80mm)' : ($isA4Dual ? 'Máy in văn phòng A4 (2 Liên chia đôi)' : 'Máy in văn phòng A5 (Mặc định)') }}</b></span>
        </div>
        <div class="flex items-center space-x-1.5 flex-wrap">
            <a href="{{ request()->fullUrlWithQuery(['format' => null]) }}" class="px-2.5 py-1.5 rounded-lg font-bold text-xs transition {{ (!$isK80 && !$isA4Dual) ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                📄 Khổ A5 (Mặc định)
            </a>
            <a href="{{ request()->fullUrlWithQuery(['format' => 'a4_dual']) }}" class="px-2.5 py-1.5 rounded-lg font-bold text-xs transition {{ $isA4Dual ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                📋 Khổ A4 (2 Liên)
            </a>
            <a href="{{ request()->fullUrlWithQuery(['format' => 'k80']) }}" class="px-2.5 py-1.5 rounded-lg font-bold text-xs transition {{ $isK80 ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                🧾 Khổ K80 (Nhiệt 80mm)
            </a>
            @if($order->is_vat_invoice)
            <a href="{{ url("/print/vat-invoice/{$order->id}") }}" class="px-2.5 py-1.5 rounded-lg font-bold text-xs bg-amber-600 hover:bg-amber-500 text-white transition">
                📄 Hóa Đơn VAT
            </a>
            @endif
            <button onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-3.5 py-1.5 rounded-lg shadow-xs flex items-center space-x-1 cursor-pointer transition">
                <span>🖨️ BẤM ĐỂ IN (CTRL + P)</span>
            </button>
            <button onclick="window.close()" class="bg-slate-700 hover:bg-slate-600 text-white px-2.5 py-1.5 rounded-lg text-xs transition">Đóng</button>
        </div>
    </div>

    @php
        $copies = $isA4Dual ? ['LIÊN 1: LƯU CỬA HÀNG', 'LIÊN 2: GIAO CHO KHÁCH HÀNG'] : [''];
    @endphp

    <div class="{{ $isK80 ? 'max-w-[76mm] w-[76mm] p-1 my-14' : ($isA4Dual ? 'max-w-[190mm] p-3 my-14' : 'max-w-[148mm] p-4 my-14') }} mx-auto bg-white shadow-md border border-slate-300 print-page">

        @foreach($copies as $copyIndex => $copyLabel)

            @if($copyIndex > 0)
                <div class="cut-line avoid-break">
                    <span class="cut-line-text">✂ ĐƯỜNG CẮT RỜI — LIÊN 1: LƯU CỬA HÀNG / LIÊN 2: GIAO KHÁCH HÀNG ✂</span>
                </div>
            @endif

            <div class="{{ $isA4Dual ? 'p-3 bg-white border border-slate-200 rounded-lg' : '' }}">
                
                @if($isA4Dual)
                <div class="flex justify-between items-center text-[10px] font-bold text-slate-500 pb-1 mb-1 border-b border-slate-200">
                    <span class="text-indigo-700 font-extrabold">{{ $copyLabel }}</span>
                    <span>Số HĐ: <b class="font-mono text-slate-900">{{ $order->order_code }}</b></span>
                </div>
                @endif

                <!-- Shop Header -->
                <div class="border-b border-slate-300 pb-2 mb-2 {{ $isK80 ? 'text-center' : 'flex justify-between items-start' }}">
                    <div class="{{ $isK80 ? 'w-full' : 'max-w-[70%]' }}">
                        <h1 class="{{ $isK80 ? 'text-sm' : ($isA4Dual ? 'text-sm' : 'text-base') }} font-black uppercase text-indigo-950 leading-tight">
                            {{ $storefrontSettings['site_name'] ?? 'CỬA HÀNG VĂN PHÒNG PHẨM & DỊCH VỤ MÁY IN' }}
                        </h1>
                        @if(!empty($storefrontSettings['address']))
                            <p class="text-[10px] text-slate-600 mt-0.5 leading-tight">Đ/C: {{ $storefrontSettings['address'] }}</p>
                        @endif
                        <div class="text-[10px] text-slate-600 flex flex-wrap gap-x-3 mt-0.5">
                            @if(!empty($storefrontSettings['hotline']))<span>Hotline: <b>{{ $storefrontSettings['hotline'] }}</b></span>@endif
                            @if(!empty($storefrontSettings['zalo']))<span>Zalo: <b>{{ $storefrontSettings['zalo'] }}</b></span>@endif
                        </div>
                    </div>

                    <!-- Right Column: Title & QR Code -->
                    <div class="{{ $isK80 ? 'mt-2 text-center' : 'text-right shrink-0' }}">
                        <h2 class="{{ $isK80 ? 'text-base' : ($isA4Dual ? 'text-base' : 'text-lg') }} font-black uppercase text-slate-800 leading-none">
                            HÓA ĐƠN BÁN LẺ
                        </h2>
                        <div class="text-[10px] text-slate-500 mt-1 space-y-0.5">
                            <div>Số: <b class="font-mono text-slate-900 text-xs">{{ $order->order_code }}</b></div>
                            <div>Ngày: {{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Customer & Delivery Info -->
                <div class="text-[11px] text-slate-700 pb-2 mb-2 border-b border-slate-200 grid grid-cols-1 sm:grid-cols-12 gap-1.5 items-center">
                    <div class="sm:col-span-8 space-y-0.5">
                        <p>Khách hàng: <b class="text-slate-900">{{ $order->customer_name ?: 'Khách lẻ vãng lai' }}</b> {{ $order->customer_phone ? "({$order->customer_phone})" : '' }}</p>
                        @if($order->customer_address)
                        <p class="leading-tight">Địa chỉ: <span class="text-slate-800">{{ $order->customer_address }}</span></p>
                        @endif
                        @if($order->notes)
                        <p class="italic text-[10px] text-slate-500">Ghi chú: {{ $order->notes }}</p>
                        @endif
                    </div>
                    <div class="sm:col-span-4 sm:text-right text-[10px] text-slate-500">
                        <p>Kênh: <b>{{ $order->channel === 'pos' ? 'Tại quầy POS' : 'Website Trực tuyến' }}</b></p>
                        <p>Thu ngân: {{ $order->creator?->name ?: 'Thu ngân tại quầy' }}</p>
                    </div>
                </div>

                @if($order->is_vat_invoice)
                <div class="text-[10px] bg-slate-50 p-1.5 rounded border border-slate-200 mb-2">
                    <p class="font-bold text-amber-900">Yêu cầu xuất HĐ VAT: {{ $order->company_name }} (MST: {{ $order->company_tax_id }})</p>
                    <p class="text-slate-500">Đ/C: {{ $order->company_address }} | Email: {{ $order->invoice_email }}</p>
                </div>
                @endif

                <!-- Order Items Display -->
                @if($isK80)
                <!-- K80 Layout -->
                <div class="border-t border-b border-dashed border-slate-400 py-1.5 my-2">
                    <div class="flex justify-between font-bold text-[10px] text-slate-700 pb-1 border-b border-slate-200">
                        <span>MẶT HÀNG</span>
                        <span>T.TIỀN</span>
                    </div>
                    @foreach($order->orderItems as $index => $item)
                    <div class="py-1 border-b border-slate-100 last:border-0 text-[10px]">
                        <div class="font-semibold text-slate-900 leading-tight">{{ $item->product_name }}</div>
                        <div class="flex justify-between text-[9px] text-slate-600 font-mono mt-0.5">
                            <span>{{ $item->quantity }} {{ $item->unit_name }} x {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                            <span class="font-bold text-slate-900">{{ number_format($item->subtotal, 0, ',', '.') }} ₫</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <!-- A5 / A4 Table Layout -->
                <table class="w-full text-[11px] text-left border-collapse border border-slate-300 mb-2">
                    <thead class="bg-slate-100 font-bold text-slate-700 text-[10px]">
                        <tr>
                            <th class="border border-slate-300 p-1 text-center w-6">#</th>
                            <th class="border border-slate-300 p-1">Tên hàng hóa, quy cách</th>
                            <th class="border border-slate-300 p-1 text-center w-10">ĐVT</th>
                            <th class="border border-slate-300 p-1 text-center w-8">SL</th>
                            <th class="border border-slate-300 p-1 text-right w-16">Đơn giá</th>
                            <th class="border border-slate-300 p-1 text-right w-20">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $index => $item)
                        <tr>
                            <td class="border border-slate-300 p-1 text-center">{{ $index + 1 }}</td>
                            <td class="border border-slate-300 p-1 font-medium leading-tight">{{ $item->product_name }}</td>
                            <td class="border border-slate-300 p-1 text-center">{{ $item->unit_name ?: 'Cái' }}</td>
                            <td class="border border-slate-300 p-1 text-center font-bold font-mono">{{ $item->quantity }}</td>
                            <td class="border border-slate-300 p-1 text-right font-mono">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="border border-slate-300 p-1 text-right font-mono font-bold">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                <!-- Totals & Payment Summary -->
                <div class="text-[11px] border-t border-slate-300 pt-1.5 space-y-1 avoid-break">
                    <div class="flex justify-between text-slate-600">
                        <span>Tiền hàng:</span>
                        <span class="font-mono font-semibold">{{ number_format($order->subtotal, 0, ',', '.') }} ₫</span>
                    </div>

                    @if($order->discount_amount > 0)
                    <div class="flex justify-between text-amber-700">
                        <span>Chiết khấu / Giảm giá:</span>
                        <span class="font-mono">-{{ number_format($order->discount_amount, 0, ',', '.') }} ₫</span>
                    </div>
                    @endif

                    @if($order->shipping_fee > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Phí giao hàng:</span>
                        <span class="font-mono font-semibold">{{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</span>
                    </div>
                    @endif

                    @if($order->tax_amount > 0)
                    <div class="flex justify-between text-slate-500 text-[10px]">
                        <span>Thuế VAT ({{ $order->tax_rate }}%):</span>
                        <span class="font-mono">{{ number_format($order->tax_amount, 0, ',', '.') }} ₫</span>
                    </div>
                    @endif

                    <div class="flex justify-between items-baseline pt-1 border-t border-slate-400 font-black text-slate-900">
                        <span class="uppercase text-xs">TỔNG CỘNG:</span>
                        <span class="{{ $isK80 ? 'text-xs' : 'text-sm' }} text-rose-600 font-mono font-bold">{{ number_format($order->grand_total, 0, ',', '.') }} ₫</span>
                    </div>

                    <div class="flex justify-between text-[10px] text-slate-600 pt-0.5">
                        <span>Hình thức: {{ $order->payment_method === 'vietqr' ? 'VietQR Động' : ($order->payment_method === 'transfer' ? 'Chuyển khoản' : ($order->payment_method === 'cod' ? 'Thu hộ COD' : 'Tiền mặt')) }}</span>
                        <span class="font-bold {{ $order->payment_status === 'paid' ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $order->payment_status === 'paid' ? 'ĐÃ THANH TOÁN ĐỦ' : 'CHƯA THANH TOÁN' }}
                        </span>
                    </div>

                    @if($order->payment_method === 'cash')
                        @if($order->cash_received !== null)
                            <div class="flex justify-between text-[10px]">
                                <span>Tiền khách đưa:</span>
                                <span class="font-mono font-semibold">{{ number_format($order->cash_received, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="flex justify-between text-[10px]">
                                <span>Tiền thừa trả khách:</span>
                                <span class="font-mono font-semibold">{{ number_format($paymentSummary['change'] ?? max(0, $order->cash_received - $order->grand_total), 0, ',', '.') }} đ</span>
                            </div>
                        @else
                            <p class="text-slate-500 text-[10px] italic">Đơn cũ chưa lưu thông tin tiền khách đưa và tiền thừa.</p>
                        @endif
                    @endif
                </div>

                <!-- VietQR & Lookup QR Section -->
                <div class="mt-2.5 pt-2 border-t border-slate-200 flex items-center justify-between gap-2 avoid-break">
                    <!-- QR Tra cứu tiến độ đơn hàng -->
                    @if(!empty($lookupUrl))
                    <div class="flex items-center space-x-2 bg-slate-50 p-1.5 rounded-lg border border-slate-200">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data={{ urlencode($lookupUrl) }}" alt="QR Tra cứu" class="w-12 h-12 bg-white p-0.5 rounded border border-slate-300" />
                        <div class="text-[9px] leading-tight">
                            <b class="text-indigo-900 uppercase block">QUÉT QR TRA CỨU:</b>
                            <span class="text-slate-500">Xem tiến độ giao hàng và bảo hành sản phẩm.</span>
                        </div>
                    </div>
                    @endif

                    <!-- QR Thanh toán VietQR nếu chưa trả đủ -->
                    @if($order->payment_status !== 'paid' && !empty($vietQrUrl))
                    <div class="flex items-center space-x-2 bg-emerald-50 p-1.5 rounded-lg border border-emerald-200">
                        <img src="{{ $vietQrUrl }}" alt="VietQR" class="w-12 h-12 bg-white p-0.5 rounded border border-emerald-300" />
                        <div class="text-[9px] leading-tight">
                            <b class="text-emerald-900 uppercase block">QUÉT VIETQR TRẢ TIỀN:</b>
                            <span class="text-slate-600">Khớp tiền tự động trong 3 giây.</span>
                        </div>
                    </div>
                    @endif
                </div>

                @if(!$isK80)
                <!-- Chữ ký 3 bên chuẩn mực (Người lập - Người giao - Người nhận) -->
                <div class="mt-4 pt-2 border-t border-slate-300 grid grid-cols-3 gap-2 text-center text-[10px] avoid-break">
                    <div>
                        <span class="font-bold text-slate-800 uppercase block">NGƯỜI LẬP PHIẾU</span>
                        <span class="text-[9px] text-slate-400 italic block">(Ký, ghi rõ họ tên)</span>
                        <div class="h-10"></div>
                        <span class="font-medium text-slate-700">{{ $order->creator?->name ?: 'Thu ngân' }}</span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-800 uppercase block">NGƯỜI GIAO HÀNG</span>
                        <span class="text-[9px] text-slate-400 italic block">(Ký, ghi rõ họ tên)</span>
                        <div class="h-10"></div>
                    </div>
                    <div>
                        <span class="font-bold text-slate-800 uppercase block">NGƯỜI NHẬN HÀNG</span>
                        <span class="text-[9px] text-slate-400 italic block">(Ký, ghi rõ họ tên)</span>
                        <div class="h-10"></div>
                        <span class="font-medium text-slate-700">{{ $order->customer_name }}</span>
                    </div>
                </div>
                @endif

                <!-- Footer greeting -->
                <div class="mt-3 text-center text-[9px] text-slate-500 border-t border-slate-200 pt-1.5 avoid-break">
                    <p class="font-bold text-slate-800">Cảm ơn Quý khách & Hẹn gặp lại!</p>
                    <p class="italic">Quý khách vui lòng kiểm tra kỹ số lượng & quy cách hàng hóa trước khi ký nhận.</p>
                </div>

            </div>

        @endforeach

    </div>

    @if(request()->routeIs('pos.receipt') && request()->boolean('autoprint'))
        <script src="{{ asset('js/pos-receipt.js') }}?v={{ filemtime(public_path('js/pos-receipt.js')) }}" defer></script>
    @endif
</body>
</html>
