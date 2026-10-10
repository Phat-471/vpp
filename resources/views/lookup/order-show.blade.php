@extends('layouts.storefront')

@section('title', 'Chi Tiết Đơn Hàng #' . $order->order_code . ' | ' . ($storefrontSettings['site_name'] ?? 'VPP'))
@section('meta_description', 'Tra cứu chi tiết và tiến độ giao hàng đơn hàng #' . $order->order_code)

@section('schema_extra')
    <script type="application/ld+json">
        {
          "@@context": "https://schema.org",
          "@type": "BreadcrumbList",
          "itemListElement": [
            {
              "@type": "ListItem",
              "position": 1,
              "name": "Trang chủ",
              "item": "{{ route('storefront.index') }}"
            },
            {
              "@type": "ListItem",
              "position": 2,
              "name": "Tra cứu tiến độ",
              "item": "{{ route('lookup.index') }}"
            },
            {
              "@type": "ListItem",
              "position": 3,
              "name": "Đơn hàng {{ $order->order_code }}",
              "item": "{{ url()->current() }}"
            }
          ]
        }
    </script>
@endsection

@section('content')
<main class="max-w-4xl mx-auto w-full px-4 sm:px-6 py-6 sm:py-10 space-y-6">

    <!-- Top Action / Navigation Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-200">
        <a href="{{ route('lookup.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Tra cứu đơn khác
        </a>
        <div class="flex items-center space-x-2">
            <a href="{{ route('print.order', $order->id) }}" target="_blank" class="inline-flex items-center space-x-1.5 px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl border border-indigo-200 shadow-2xs transition">
                <span>🖨️</span>
                <span>In / Xem Hóa Đơn</span>
            </a>
            @if($order->is_vat_invoice)
            <a href="{{ route('print.vat-invoice', $order->id) }}" target="_blank" class="inline-flex items-center space-x-1 px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs rounded-xl border border-amber-200 transition">
                <span>📄 Hóa Đơn VAT</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-5 sm:p-7 border border-slate-200 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">ĐƠN HÀNG VĂN PHÒNG PHẨM</span>
                <div class="flex items-center space-x-2.5 mt-0.5">
                    <h1 class="text-2xl sm:text-3xl font-black font-mono text-slate-900">#{{ $order->order_code }}</h1>
                    @if($order->payment_status === 'paid')
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-black text-xs rounded-full border border-emerald-200">
                            ✓ Đã thanh toán
                        </span>
                    @elseif($order->payment_status === 'partially_paid')
                        <span class="px-3 py-1 bg-amber-100 text-amber-800 font-black text-xs rounded-full border border-amber-200">
                            Thanh toán một phần
                        </span>
                    @else
                        <span class="px-3 py-1 bg-rose-100 text-rose-800 font-black text-xs rounded-full border border-rose-200">
                            Chưa thanh toán
                        </span>
                    @endif
                </div>
            </div>
            <div class="text-left sm:text-right text-xs text-slate-500 space-y-0.5">
                <div>Thời gian đặt: <b class="text-slate-700">{{ $order->created_at->format('d/m/Y H:i') }}</b></div>
                <div>Kênh bán: <b class="text-slate-700">{{ $order->channel === 'pos' ? 'Tại quầy POS' : 'Website Trực tuyến' }}</b></div>
            </div>
        </div>

        <!-- Timeline Tiến Độ Đơn Hàng -->
        @php
            $statuses = [
                'pending' => ['label' => 'Đã tiếp nhận', 'icon' => '📝', 'desc' => 'Đơn hàng đã ghi nhận vào hệ thống'],
                'processing' => ['label' => 'Đang chuẩn bị', 'icon' => '📦', 'desc' => 'Kho đang đóng gói sản phẩm'],
                'shipping' => ['label' => 'Đang giao hàng', 'icon' => '🚚', 'desc' => 'Shipper đang trên đường giao'],
                'delivered' => ['label' => 'Đã giao thành công', 'icon' => '🎉', 'desc' => 'Khách đã nhận được hàng'],
            ];

            $orderStatus = $order->status ?? 'pending';
            $isCancelled = in_array($orderStatus, ['cancelled', 'canceled']);

            $statusOrder = ['pending', 'processing', 'shipping', 'delivered'];
            $currentIndex = array_search($orderStatus, $statusOrder);
            if ($currentIndex === false) $currentIndex = 0;
        @endphp

        @if($isCancelled)
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 flex items-center space-x-3">
                <span class="text-2xl">❌</span>
                <div>
                    <h4 class="font-black text-sm">ĐƠN HÀNG ĐÃ BỊ HỦY</h4>
                    <p class="text-xs text-rose-600 mt-0.5">Đơn hàng này đã bị hủy bỏ trên hệ thống. Nếu có thắc mắc, vui lòng liên hệ nhân viên cửa hàng.</p>
                </div>
            </div>
        @else
            <div class="pt-2">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-4">TIẾN ĐỘ THỰC HIỆN</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach($statusOrder as $idx => $stepKey)
                        @php
                            $step = $statuses[$stepKey];
                            $isPassed = $idx <= $currentIndex;
                            $isCurrent = $idx === $currentIndex;
                        @endphp
                        <div class="p-3.5 rounded-2xl border transition {{ $isCurrent ? 'bg-indigo-50/80 border-indigo-400 shadow-2xs' : ($isPassed ? 'bg-slate-50 border-slate-200' : 'bg-slate-50/50 border-slate-100 opacity-60') }}">
                            <div class="flex items-center space-x-2">
                                <span class="text-lg">{{ $step['icon'] }}</span>
                                <span class="font-black text-xs {{ $isCurrent ? 'text-indigo-900' : ($isPassed ? 'text-slate-800' : 'text-slate-400') }}">
                                    {{ $step['label'] }}
                                </span>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1.5 leading-snug">{{ $step['desc'] }}</p>
                            @if($isCurrent)
                                <div class="mt-2 inline-flex items-center space-x-1 text-[9px] font-black text-indigo-700 uppercase bg-indigo-100 px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-ping"></span>
                                    <span>Đang ở bước này</span>
                                </div>
                            @elseif($isPassed)
                                <div class="mt-2 inline-flex items-center space-x-1 text-[9px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                                    <span>✓ Hoàn thành</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- VietQR Thanh Toán Bổ Sung (Nếu chưa thanh toán & không phải đơn hủy) -->
    @if(!$isCancelled && $order->payment_status !== 'paid' && !empty($vietQrUrl))
        <div class="bg-gradient-to-br from-indigo-50 via-slate-50 to-emerald-50 rounded-3xl p-5 sm:p-7 border border-indigo-200 shadow-xs space-y-4">
            <div class="flex items-center space-x-3">
                <span class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg font-black shrink-0">
                    ⚡
                </span>
                <div>
                    <h3 class="text-base font-black text-slate-900">Quét Mã VietQR Để Thanh Toán</h3>
                    <p class="text-xs text-slate-500">Mở ứng dụng ngân hàng bất kỳ để quét mã thanh toán tự động trong 3 giây.</p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row items-center gap-6 pt-2">
                <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-200 shrink-0 text-center">
                    <img src="{{ $vietQrUrl }}" alt="VietQR Thanh Toán" class="w-48 h-48 mx-auto object-contain rounded-lg" />
                    <span class="text-[10px] text-slate-400 mt-1 block">Tự động điền đúng số tiền & nội dung</span>
                </div>

                <div class="flex-1 w-full space-y-2.5 text-xs">
                    <div class="p-3 bg-white rounded-2xl border border-slate-200 flex justify-between items-center">
                        <span class="text-slate-500">Số tiền cần thanh toán:</span>
                        <span class="font-mono font-black text-rose-600 text-base">
                            {{ \App\Helpers\AppHelper::formatMoney(max(0, (float) $order->grand_total - (float) $order->paid_amount)) }}
                        </span>
                    </div>

                    <div class="p-3 bg-white rounded-2xl border border-slate-200 flex justify-between items-center">
                        <span class="text-slate-500">Nội dung chuyển khoản:</span>
                        <div class="flex items-center space-x-2">
                            <span class="font-mono font-black text-indigo-700 text-sm" id="qr-transfer-content">{{ $order->order_code }}</span>
                            <button type="button" onclick="copyText('{{ $order->order_code }}', 'Đã sao chép mã đơn hàng!')" class="px-2 py-0.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-[10px] font-bold border border-indigo-200 transition">
                                Sao chép
                            </button>
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        💡 <b>Lưu ý:</b> Giữ nguyên nội dung chuyển khoản là <b class="font-mono text-indigo-700">{{ $order->order_code }}</b> để hệ thống tự động gạch nợ ngay khi nhận tiền.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Danh Sách Sản Phẩm & Chi Tiết Thanh Toán -->
    <div class="bg-white rounded-3xl p-5 sm:p-7 border border-slate-200 shadow-xs space-y-4">
        <h3 class="text-sm font-black uppercase tracking-wider text-slate-800">DANH SÁCH MẶT HÀNG</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase font-black text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3">Sản phẩm</th>
                        <th class="py-3 px-2 text-center">ĐVT</th>
                        <th class="py-3 px-2 text-center">SL</th>
                        <th class="py-3 px-3 text-right">Đơn giá</th>
                        <th class="py-3 px-3 text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($order->orderItems as $item)
                    <tr>
                        <td class="py-3 px-3">
                            <span class="font-bold text-slate-900 block">{{ $item->product_name }}</span>
                            @if(!empty($item->sku))
                                <span class="text-[10px] text-slate-400 font-mono">Mã: {{ $item->sku }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-2 text-center text-slate-600">{{ $item->unit_name ?: 'Cái' }}</td>
                        <td class="py-3 px-2 text-center font-mono font-bold text-slate-900">{{ $item->quantity }}</td>
                        <td class="py-3 px-3 text-right font-mono text-slate-600">
                            {{ \App\Helpers\AppHelper::formatMoney($item->unit_price) }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono font-bold text-slate-900">
                            {{ \App\Helpers\AppHelper::formatMoney($item->subtotal) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tổng kết hóa đơn -->
        <div class="pt-4 border-t border-slate-200 space-y-2 text-xs max-w-sm ml-auto">
            <div class="flex justify-between text-slate-600">
                <span>Tiền hàng:</span>
                <span class="font-mono font-semibold">{{ \App\Helpers\AppHelper::formatMoney($order->subtotal) }}</span>
            </div>

            @if($order->discount_amount > 0)
            <div class="flex justify-between text-amber-700">
                <span>Chiết khấu / Khuyến mãi:</span>
                <span class="font-mono">-{{ \App\Helpers\AppHelper::formatMoney($order->discount_amount) }}</span>
            </div>
            @endif

            @if($order->shipping_fee > 0)
            <div class="flex justify-between text-slate-600">
                <span>Phí giao hàng:</span>
                <span class="font-mono font-semibold">{{ \App\Helpers\AppHelper::formatMoney($order->shipping_fee) }}</span>
            </div>
            @endif

            @if($order->tax_amount > 0)
            <div class="flex justify-between text-slate-500 text-[11px]">
                <span>Thuế VAT ({{ $order->tax_rate }}%):</span>
                <span class="font-mono">{{ \App\Helpers\AppHelper::formatMoney($order->tax_amount) }}</span>
            </div>
            @endif

            <div class="flex justify-between items-baseline pt-2 border-t border-slate-300 text-sm font-black text-slate-900">
                <span class="uppercase">TỔNG CỘNG:</span>
                <span class="text-base text-rose-600 font-mono font-bold">
                    {{ \App\Helpers\AppHelper::formatMoney($order->grand_total) }}
                </span>
            </div>

            <div class="flex justify-between text-[11px] text-slate-500 pt-1">
                <span>Đã thanh toán:</span>
                <span class="font-mono font-bold text-emerald-700">{{ \App\Helpers\AppHelper::formatMoney($order->paid_amount) }}</span>
            </div>
        </div>
    </div>

    <!-- Thông Tin Người Nhận & Giao Hàng -->
    <div class="bg-white rounded-3xl p-5 sm:p-7 border border-slate-200 shadow-xs space-y-4">
        <h3 class="text-sm font-black uppercase tracking-wider text-slate-800">THÔNG TIN GIAO HÀNG & LIÊN HỆ</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Người nhận hàng</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $order->customer_name }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Số điện thoại</span>
                    <span class="font-mono font-bold text-indigo-700">{{ \App\Helpers\AppHelper::maskPhone($order->customer_phone) }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Địa chỉ giao hàng</span>
                    <span class="text-slate-700 leading-snug block">{{ $order->customer_address ?: 'Nhận tại cửa hàng' }}</span>
                </div>
            </div>

            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Phương thức thanh toán</span>
                    <span class="font-bold text-slate-900">
                        @if($order->payment_method === 'vietqr')
                            Chuyển khoản VietQR Động
                        @elseif($order->payment_method === 'transfer')
                            Chuyển khoản Ngân hàng
                        @elseif($order->payment_method === 'cod')
                            Thanh toán khi nhận hàng (COD)
                        @else
                            Tiền mặt
                        @endif
                    </span>
                </div>
                @if($order->notes)
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Ghi chú giao hàng</span>
                    <p class="text-slate-700 italic">{{ $order->notes }}</p>
                </div>
                @endif
                @if($order->is_vat_invoice)
                <div class="pt-2 border-t border-slate-200">
                    <span class="text-amber-800 font-bold block text-[11px]">🏢 Đã yêu cầu xuất Hóa Đơn VAT</span>
                    <p class="text-[11px] text-slate-600">{{ $order->company_name }} - MST: {{ $order->company_tax_id }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Buttons Footer -->
    <div class="flex flex-col sm:flex-row gap-3 pt-2">
        <a href="{{ route('storefront.products') }}" class="flex-1 py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm rounded-2xl shadow-xs transition text-center flex items-center justify-center space-x-1.5">
            <span>🛍️</span>
            <span>Tiếp Tục Mua Hàng</span>
        </a>
        <a href="https://zalo.me/{{ $storefrontSettings['zalo'] ?? '' }}" target="_blank" class="py-3 px-6 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm rounded-2xl transition text-center flex items-center justify-center space-x-1.5">
            <span>💬</span>
            <span>Hỗ Trợ Zalo</span>
        </a>
    </div>

</main>

@push('scripts')
<script>
    function copyText(text, successMsg) {
        if (!navigator.clipboard) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
        } else {
            navigator.clipboard.writeText(text);
        }
        if (typeof showToast === 'function') {
            showToast(successMsg, 'success');
        } else {
            alert(successMsg);
        }
    }
</script>
@endpush
@endsection
