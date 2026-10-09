<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn GTGT - {{ $order->order_code }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Times New Roman', Times, serif, sans-serif;
            font-size: 13px;
            line-height: 1.4;
            color: #000;
            background: #f1f5f9;
            padding: 20px 0;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 20mm;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            position: relative;
        }
        .border-box {
            border: 1.5px solid #1e3a8a;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 12px;
        }
        .header-title {
            text-align: center;
            margin-bottom: 15px;
        }
        .header-title h1 {
            font-size: 20px;
            text-transform: uppercase;
            color: #1e3a8a;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .header-title .sub-title {
            font-size: 11px;
            font-style: italic;
            color: #475569;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
            margin-bottom: 12px;
        }
        .company-info p, .buyer-info p {
            margin-bottom: 3px;
            font-size: 12.5px;
        }
        .label {
            font-weight: bold;
            color: #1e293b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #334155;
            padding: 6px 8px;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            color: #0f172a;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .summary-table td {
            border: none;
            padding: 4px 8px;
        }
        .amount-in-words {
            font-style: italic;
            margin: 8px 0;
            padding: 6px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            font-size: 12.5px;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-top: 25px;
            text-align: center;
        }
        .sig-block {
            padding: 5px;
        }
        .sig-title {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 3px;
        }
        .sig-note {
            font-size: 11px;
            font-style: italic;
            color: #64748b;
        }
        .stamp-box {
            margin-top: 50px;
            color: #dc2626;
            font-weight: bold;
            font-size: 11px;
            border: 1.5px dashed #dc2626;
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
        }
        .print-btn-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-print {
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 10px 24px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-print:hover { background: #4338ca; }
        @media print {
            body { background: #fff; padding: 0; }
            .print-btn-bar { display: none; }
            .page { border: none; box-shadow: none; padding: 10mm 15mm; width: 100%; }
        }
    </style>
</head>
<body>

    <div class="print-btn-bar">
        <button class="btn-print" onclick="window.print()">🖨️ In Hóa Đơn GTGT (Khổ A4)</button>
    </div>

    <div class="page">
        <!-- HEADER TIÊU ĐỀ HÓA ĐƠN -->
        <div class="header-title">
            <h1>HÓA ĐƠN GIÁ TRỊ GIA TĂNG (VAT)</h1>
            <p class="sub-title">(Bản thể hiện của hóa đơn điện tử - Hợp pháp theo Nghị định 123/2020/NĐ-CP & Thông tư 78/2021/TT-BTC)</p>
            <p style="margin-top: 4px; font-size: 12px;">Ngày {{ $order->created_at->format('d') }} tháng {{ $order->created_at->format('m') }} năm {{ $order->created_at->format('Y') }}</p>
        </div>

        <div class="info-grid">
            <div>
                <p><span class="label">Mẫu số:</span> 1/001</p>
                <p><span class="label">Ký hiệu (Serial):</span> 1C26TAD</p>
            </div>
            <div style="text-align: right;">
                <p><span class="label">Số hóa đơn:</span> <strong style="font-size: 15px; color: #dc2626;">{{ $order->order_code }}</strong></p>
                <p><span class="label">Mã tra cứu:</span> {{ substr(strtoupper($order->uuid ?? md5($order->id)), 0, 8) }}</p>
            </div>
        </div>

        <!-- THÔNG TIN ĐƠN VỊ BÁN HÀNG -->
        <div class="border-box">
            <div class="company-info">
                <p><span class="label">Đơn vị bán hàng:</span> <strong style="text-transform: uppercase; color: #1e3a8a;">{{ setting('company_name', 'CÔNG TY TNHH THƯƠNG MẠI VÀ DỊCH VỤ VPP') }}</strong></p>
                <p><span class="label">Mã số thuế:</span> <strong style="font-size: 13px; letter-spacing: 1px;">{{ setting('company_tax_id', '0109887766') }}</strong></p>
                <p><span class="label">Địa chỉ:</span> {{ setting('company_address', '30 Bình Hòa, Đồng Nai, Việt Nam') }}</p>
                <p><span class="label">Điện thoại / Hotline:</span> {{ setting('hotline', '0974.194.305') }}</p>
                <p><span class="label">Số tài khoản:</span> {{ setting('vietqr_account_number', '9974194305') }} tại {{ setting('vietqr_bank_name', 'Techcombank (TCB)') }}</p>
            </div>
        </div>

        <!-- THÔNG TIN ĐƠN VỊ MUA HÀNG -->
        <div class="border-box">
            <div class="buyer-info">
                <p><span class="label">Họ tên người mua hàng:</span> {{ $order->customer_name ?? 'Khách hàng' }}</p>
                <p><span class="label">Tên đơn vị:</span> <strong>{{ $order->company_name ?: ($order->customer->company_name ?? 'Khách lẻ / Không lấy hóa đơn tên công ty') }}</strong></p>
                <p><span class="label">Mã số thuế:</span> <strong>{{ $order->company_tax_id ?: 'Chưa cung cấp' }}</strong></p>
                <p><span class="label">Địa chỉ công ty:</span> {{ $order->company_address ?: ($order->customer_address ?: 'Hà Nội') }}</p>
                <p><span class="label">Hình thức thanh toán:</span> {{ $order->payment_method === 'vietqr' ? 'Chuyển khoản VietQR' : ($order->payment_method === 'transfer' ? 'Chuyển khoản ngân hàng' : 'Tiền mặt') }} - <span class="label">Đồng tiền thanh toán:</span> VNĐ</p>
            </div>
        </div>

        <!-- BẢNG DANH MỤC HÀNG HÓA -->
        <table>
            <thead>
                <tr>
                    <th style="width: 35px;">STT</th>
                    <th>Tên hàng hóa, dịch vụ</th>
                    <th style="width: 55px;">ĐVT</th>
                    <th style="width: 50px;">Số lượng</th>
                    <th style="width: 95px;">Đơn giá</th>
                    <th style="width: 105px;">Thành tiền</th>
                </tr>
                <tr style="font-size: 10px; font-weight: normal; color: #64748b; background: #fff;">
                    <th>(1)</th>
                    <th>(2)</th>
                    <th>(3)</th>
                    <th>(4)</th>
                    <th>(5)</th>
                    <th>(6 = 4 x 5)</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $subtotal = 0; 
                @endphp
                @foreach($order->orderItems as $index => $item)
                    @php
                        $lineTotal = (float) $item->unit_price * (float) $item->quantity;
                        $subtotal += $lineTotal;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left font-semibold">{{ $item->product_name }}</td>
                        <td class="text-center">{{ $item->unit_name }}</td>
                        <td class="text-center font-bold">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right font-bold">{{ number_format($lineTotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- BẢNG TỔNG KẾT THUẾ VAT -->
        @php
            $taxRate = (float) ($order->tax_rate ?: setting('default_vat_rate', 8));
            $taxAmount = (float) ($order->tax_amount ?: round($subtotal * ($taxRate / 100)));
            $grandTotal = (float) ($order->grand_total ?: ($subtotal + $taxAmount));
        @endphp

        <table class="summary-table">
            <tr>
                <td style="width: 65%; text-align: right; font-weight: bold;">Cộng tiền hàng (chưa bao gồm VAT):</td>
                <td style="width: 35%; text-align: right; font-weight: bold; font-size: 13px;">{{ number_format($subtotal, 0, ',', '.') }} đ</td>
            </tr>
            @if($order->discount_amount > 0)
            <tr>
                <td style="text-align: right; color: #dc2626;">Chiết khấu thương mại:</td>
                <td style="text-align: right; color: #dc2626;">-{{ number_format($order->discount_amount, 0, ',', '.') }} đ</td>
            </tr>
            @endif
            <tr>
                <td style="text-align: right; font-weight: bold;">Thuế suất GTGT: <strong>{{ (int) $taxRate }}%</strong> - Tiền thuế GTGT:</td>
                <td style="text-align: right; font-weight: bold; font-size: 13px;">{{ number_format($taxAmount, 0, ',', '.') }} đ</td>
            </tr>
            @if($order->shipping_fee > 0)
            <tr>
                <td style="text-align: right;">Cước phí vận chuyển giao hàng tận nơi:</td>
                <td style="text-align: right;">{{ number_format($order->shipping_fee, 0, ',', '.') }} đ</td>
            </tr>
            @endif
            <tr style="border-top: 1.5px solid #000;">
                <td style="text-align: right; font-size: 14px; font-weight: bold; text-transform: uppercase;">Tổng cộng tiền thanh toán:</td>
                <td style="text-align: right; font-size: 15px; font-weight: bold; color: #1e3a8a;">{{ number_format($grandTotal, 0, ',', '.') }} đ</td>
            </tr>
        </table>

        <!-- SỐ TIỀN BẰNG CHỮ -->
        <div class="amount-in-words">
            <strong>Số tiền viết bằng chữ:</strong> <em>{{ vietnamese_number_to_words($grandTotal) }}</em>
        </div>

        <!-- KHUNG CHỮ KÝ PHÁP LÝ -->
        <div class="signatures">
            <div class="sig-block">
                <div class="sig-title">NGƯỜI MUA HÀNG</div>
                <div class="sig-note">(Ký, ghi rõ họ tên)</div>
                <div style="height: 60px;"></div>
                <p style="font-weight: bold;">{{ $order->customer_name }}</p>
            </div>
            <div class="sig-block">
                <div class="sig-title">NGƯỜI BÁN HÀNG</div>
                <div class="sig-note">(Ký điện tử, đóng dấu)</div>
                <div class="stamp-box">
                    ✓ KÝ BỞI: {{ strtoupper(setting('company_name', 'VPP & THIẾT BỊ MÁY IN')) }}<br>
                    NGÀY KÝ: {{ $order->created_at->format('d/m/Y H:i:s') }}
                </div>
            </div>
        </div>

        <!-- MÃ QR THANH TOÁN / TRA CỨU -->
        <div style="margin-top: 30px; border-top: 1px dashed #cbd5e1; padding-top: 12px; display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: #64748b;">
            <div>
                <p>Tra cứu hóa đơn điện tử tại: <strong>https://vppanhduong.vn/tra-cuu-hoa-don</strong></p>
                <p>Mã hóa đơn: <strong>{{ $order->order_code }}</strong> | Mã bảo mật: <strong>{{ substr(strtoupper($order->uuid ?? md5($order->id)), 0, 8) }}</strong></p>
            </div>
            @if($vietQrUrl)
                <div style="text-align: center;">
                    <img src="{{ $vietQrUrl }}" alt="VietQR Napas" style="width: 70px; height: 70px; border: 1px solid #cbd5e1; padding: 2px;">
                    <p style="font-size: 9px; margin-top: 2px;">Quét mã thanh toán</p>
                </div>
            @endif
        </div>
    </div>

</body>
</html>
