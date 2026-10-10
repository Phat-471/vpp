@extends('layouts.storefront')

@section('title', 'Giỏ Hàng & Thanh Toán | VPP & Dịch Vụ Máy In')
@section('meta_description', 'Trang giỏ hàng và thanh toán trực tuyến. Xem lại sản phẩm, điều chỉnh số lượng và đặt hàng tiện lợi.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-8">

    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs text-slate-500 font-medium">
        <a href="{{ route('storefront.index') }}" class="hover:text-indigo-600">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('storefront.products') }}" class="hover:text-indigo-600">Sản phẩm</a>
        <span>/</span>
        <span class="text-slate-900 font-bold">Giỏ hàng & Thanh toán</span>
    </nav>

    <!-- Header Box -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 flex items-center justify-between border border-indigo-900 shadow-sm">
        <div>
            <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 rounded-full text-[10px] font-black uppercase tracking-wider border border-emerald-400/30">
                GIAO HÀNG TẬN NƠI • ĐỒNG KIỂM TRƯỚC KHI TRẢ TIỀN
            </span>
            <h1 class="text-xl sm:text-3xl font-black text-white mt-1">
                Giỏ Hàng & Hoàn Tất Đặt Hàng
            </h1>
            <p class="text-xs text-slate-300 mt-1">
                Xem lại danh sách sản phẩm, điều chỉnh số lượng và điền thông tin giao nhận hàng tận nơi.
            </p>
        </div>
        <div class="text-3xl hidden sm:block">
            🛍️
        </div>
    </div>

    <!-- Empty Cart Notice (Hidden by default, shown via JS if empty) -->
    <div id="checkout-empty-cart-state" class="hidden bg-white p-12 text-center rounded-3xl border border-slate-200 shadow-xs space-y-4">
        <span class="text-5xl block">🛒</span>
        <h2 class="text-lg font-bold text-slate-800">Giỏ hàng của bạn đang trống!</h2>
        <p class="text-xs text-slate-500 max-w-sm mx-auto">
            Bạn chưa chọn sản phẩm nào để thanh toán. Hãy dạo một vòng cửa hàng để chọn thêm giấy in, bút viết hoặc hộp mực nhé.
        </p>
        <a href="{{ route('storefront.products') }}" class="inline-block px-5 py-2.5 bg-indigo-600 text-white text-xs font-bold rounded-xl shadow-xs hover:bg-indigo-700 transition">
            Khám phá 1.000+ sản phẩm →
        </a>
    </div>

    <!-- Active Checkout Form Grid (2 Columns: Cart Review 5 cols + Form 7 cols) -->
    <div id="checkout-active-section" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left: Customer Information & Payment Options (lg:col-span-7) -->
        <div class="order-2 lg:order-1 lg:col-span-7 space-y-6">
            
            <form id="main-checkout-form" onsubmit="submitCheckoutPage(event)" class="space-y-6">
                
                <!-- Honeypot chống bot spam đặt hàng ảo (Ẩn hoàn toàn, bot tự điền sẽ bị từ chối) -->
                <div style="display:none !important;" aria-hidden="true">
                    <input type="text" name="website_url" id="chk-website-url" tabindex="-1" autocomplete="off" />
                </div>

                <!-- 1. Customer Details -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h2 class="text-sm sm:text-base font-black text-slate-900 flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-black">1</span>
                            <span>Thông Tin Nhận Hàng</span>
                        </h2>
                        @if(auth('customer')->check())
                        <span class="text-xs text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-lg">
                            Đã đăng nhập: {{ auth('customer')->user()->name }}
                        </span>
                        @else
                        <a href="{{ route('customer.login') }}" class="text-xs text-indigo-600 hover:underline font-bold">
                            Đăng nhập để tự điền thông tin →
                        </a>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Họ và tên người nhận *</label>
                            <input type="text" id="chk-name" required 
                                   value="{{ auth('customer')->user()->name ?? '' }}"
                                   placeholder="VD: Nguyễn Văn An" 
                                   class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Số điện thoại nhận hàng *</label>
                            <input type="tel" id="chk-phone" required 
                                   value="{{ auth('customer')->user()->phone ?? '' }}"
                                   placeholder="VD: 0901 234 567" 
                                   class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Địa chỉ giao hàng cụ thể *</label>
                        <input type="text" id="chk-address" required 
                               value="{{ auth('customer')->user()->address ?? '' }}"
                               placeholder="VD: Số 30 Đường Bình Hòa, TP. Biên Hòa, Đồng Nai" 
                               class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Ghi chú giao hàng (nếu có)</label>
                        <input type="text" id="chk-notes" 
                               placeholder="VD: Giao trong giờ hành chính, gọi trước 15 phút..." 
                               class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
                    </div>
                </div>

                <!-- 2. Payment Method -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                    <h2 class="text-sm sm:text-base font-black text-slate-900 flex items-center space-x-2 border-b border-slate-100 pb-3">
                        <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-black">2</span>
                        <span>Phương Thức Thanh Toán</span>
                    </h2>

                    <div class="space-y-3">
                        <!-- COD -->
                        <label class="flex items-start space-x-3 p-3.5 rounded-2xl border-2 border-indigo-600 bg-indigo-50/40 cursor-pointer transition payment-radio-label">
                            <input type="radio" name="payment_method" value="cod" checked onchange="onPaymentChange(this)" class="mt-1 accent-indigo-600" />
                            <div class="flex-1">
                                <span class="font-bold text-xs text-slate-900 block">Tiền mặt khi nhận hàng (COD)</span>
                                <span class="text-[11px] text-slate-500">Thanh toán cho nhân viên giao hàng sau khi đã đồng kiểm đủ hàng.</span>
                            </div>
                            <span class="text-lg">💵</span>
                        </label>

                        <!-- VietQR -->
                        <div class="space-y-2">
                            <label class="flex items-start space-x-3 p-3.5 rounded-2xl border-2 border-slate-200 hover:border-indigo-400 bg-white cursor-pointer transition payment-radio-label">
                                <input type="radio" name="payment_method" value="vietqr" onchange="onPaymentChange(this)" class="mt-1 accent-indigo-600" />
                                <div class="flex-1">
                                    <span class="font-bold text-xs text-slate-900 block">Chuyển khoản VietQR Tự Động (Napas 24/7)</span>
                                    <span class="text-[11px] text-slate-500">Quét mã QR từ mọi App ngân hàng, hệ thống tự động nhận diện tiền về sau 3 giây.</span>
                                </div>
                                <span class="text-lg">📱</span>
                            </label>

                            <!-- VietQR Bank Info Hint (Hiện khi chọn VietQR) -->
                            <div id="vietqr-bank-hint" class="hidden p-3 rounded-2xl bg-indigo-50/70 border border-indigo-200 text-[11px] text-slate-700 space-y-1.5 animate-in fade-in">
                                <div class="flex items-center space-x-1.5 font-bold text-indigo-950">
                                    <span>🏦</span>
                                    <span>{{ $bankName }} ({{ $bankCode }})</span>
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-1 text-[11px]">
                                    <span class="text-slate-600">STK: <b class="font-mono text-slate-900">{{ $accountNumber }}</b></span>
                                    <span class="text-slate-600">Chủ TK: <b class="uppercase text-slate-900">{{ $accountName }}</b></span>
                                </div>
                                <p class="text-[10px] text-emerald-700 font-semibold pt-0.5">
                                    ✓ Mã QR tự động điền sẵn số tiền chính xác và nội dung đơn hàng ngay sau khi bấm Xác nhận.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @include('storefront.components.invoice-fields', ['invoiceId' => 'checkout-invoice'])

                <div id="checkout-error-alert" class="hidden p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-bold"></div>

                <button type="submit" id="btn-submit-checkout" class="w-full py-4 px-6 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-black text-sm rounded-2xl shadow-lg transition transform hover:-translate-y-0.5 flex items-center justify-center space-x-2">
                    <span>XÁC NHẬN ĐẶT HÀNG NGAY</span>
                    <span>→</span>
                </button>

            </form>

        </div>

        <!-- Right: Cart Items Review & Summary (lg:col-span-5) -->
        <div class="order-1 lg:order-2 lg:col-span-5 space-y-6">
            
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-black text-slate-900 text-sm">Sản Phẩm Trong Đơn</h3>
                    <span id="checkout-total-items-badge" class="text-xs text-indigo-600 font-bold">0 món</span>
                </div>

                <!-- Dynamic Freeship Progress Banner -->
                <div id="chk-freeship-banner" class="hidden p-3 rounded-2xl border text-xs space-y-1.5 transition">
                    <div class="flex items-center justify-between" id="chk-freeship-text">
                        <!-- Populated via JS -->
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                        <div id="chk-freeship-bar" class="h-1.5 rounded-full bg-emerald-500 transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Items list -->
                <div id="checkout-items-list" class="space-y-3 divide-y divide-slate-100 max-h-96 overflow-y-auto no-scrollbar">
                    <!-- Populated via JS -->
                </div>

                <!-- Price summary -->
                <div class="pt-4 border-t border-slate-100 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-500">
                        <span>Tạm tính tiền hàng:</span>
                        <span id="chk-subtotal-val" class="font-mono font-bold text-slate-800">0₫</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Phí vận chuyển:</span>
                        <span id="chk-shipping-val" class="font-bold text-emerald-600">Miễn phí giao hàng</span>
                    </div>
                    <div id="chk-vat-row" class="hidden flex justify-between text-slate-500">
                        <span>Thuế VAT (<span id="chk-vat-rate-text">{{ $defaultVatRate }}%</span>):</span>
                        <span id="chk-vat-val" class="font-mono font-bold text-slate-800">0₫</span>
                    </div>
                    <div class="flex justify-between text-base font-black text-slate-900 pt-2 border-t border-slate-200">
                        <span>Tổng Thanh Toán:</span>
                        <span id="chk-grand-total-val" class="font-mono text-lg text-indigo-700">0₫</span>
                    </div>
                </div>
            </div>

            <!-- Guarantees box -->
            <div class="p-5 rounded-3xl bg-slate-100/80 border border-slate-200 text-xs text-slate-600 space-y-2">
                <div class="flex items-center space-x-2 font-bold text-slate-900">
                    <span>🛡️ Cam Kết Của Cửa Hàng:</span>
                </div>
                <ul class="space-y-1 text-[11px] list-disc pl-4 text-slate-500">
                    <li>Khách hàng được mở kiện đồng kiểm tra hàng trước khi gửi tiền.</li>
                    <li>Đổi mới 1-đổi-1 trong vòng 7 ngày nếu sản phẩm lỗi.</li>
                    <li>Cam kết 100% hàng chính hãng, có nguồn gốc xuất xứ rõ ràng.</li>
                </ul>
            </div>

        </div>

    </div>

    <!-- Order Success Full-Screen Modal -->
    <div id="checkout-success-modal" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 text-center space-y-5 animate-in fade-in zoom-in-95">
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl mx-auto">
                ✓
            </div>
            
            <div class="space-y-1">
                <h3 class="text-xl sm:text-2xl font-black text-slate-900">Đặt Hàng Thành Công!</h3>
                <p class="text-xs text-slate-500">
                    Cảm ơn Quý khách! Đơn hàng của bạn đã được ghi nhận trên hệ thống.
                </p>
            </div>

            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-1.5 text-left">
                <div class="flex justify-between">
                    <span class="text-slate-500">Mã đơn hàng:</span>
                    <span class="font-mono font-black text-indigo-700" id="succ-order-code">#ORD-XXXX</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Tổng thanh toán:</span>
                    <span class="font-mono font-black text-slate-900 text-sm" id="succ-order-total">0₫</span>
                </div>
            </div>

            <!-- VietQR Container (If chosen) -->
            <div id="succ-vietqr-box" class="hidden p-4 bg-gradient-to-br from-indigo-50/80 via-slate-50 to-emerald-50/80 rounded-3xl border border-indigo-200 text-center space-y-3 shadow-xs">
                
                <!-- Thanh trạng thái thanh toán thời gian thực -->
                <div id="succ-status-waiting" class="flex items-center justify-center space-x-2 p-2.5 bg-amber-50 border border-amber-300 rounded-2xl text-amber-950 text-xs shadow-2xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping shrink-0"></span>
                    <span class="font-bold">Đang chờ bạn quét mã thanh toán... (Tự động nhận diện)</span>
                </div>

                <div id="succ-status-paid" class="hidden p-4 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-2xl text-center space-y-1 shadow-md">
                    <span class="text-3xl block">🎉</span>
                    <span class="font-black text-sm block uppercase tracking-wide">ĐÃ NHẬN THANH TOÁN VIETQR THÀNH CÔNG!</span>
                    <p class="text-[11px] text-emerald-100 font-medium">Hệ thống đã khớp đúng số tiền và tự động chuyển đơn sang trạng thái Đang chuẩn bị giao hàng.</p>
                </div>

                <div id="succ-qr-media-container" class="space-y-3">
                    <div class="inline-flex items-center space-x-1.5 px-3 py-1 bg-indigo-600 text-white rounded-full text-[10px] font-black uppercase tracking-wider shadow-xs">
                        <span>⚡ QUÉT MÃ VIETQR ĐỂ HOÀN TẤT</span>
                    </div>

                <div class="bg-white p-3 rounded-2xl inline-block shadow-md border border-slate-200">
                    <img id="succ-vietqr-img" src="" alt="VietQR Thanh Toán" class="w-52 h-52 mx-auto object-contain rounded-lg" />
                    <p class="text-[10px] text-slate-400 mt-1">Mở App ngân hàng bất kỳ để quét mã QR</p>
                </div>

                <!-- Chi tiết chuyển khoản & Nút sao chép -->
                <div class="p-3.5 bg-white rounded-2xl border border-slate-200 text-left text-xs space-y-2">
                    <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Ngân hàng:</span>
                        <span class="font-bold text-slate-900" id="succ-bank-name">{{ $bankName }}</span>
                    </div>

                    <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Số tài khoản:</span>
                        <div class="flex items-center space-x-1.5">
                            <span class="font-mono font-black text-indigo-700 text-sm" id="succ-account-number">{{ $accountNumber }}</span>
                            <button type="button" onclick="copyText(document.getElementById('succ-account-number').textContent, 'Đã sao chép số tài khoản!')" class="px-2 py-0.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-[10px] font-bold border border-indigo-200 transition">
                                Sao chép
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Chủ tài khoản:</span>
                        <span class="font-bold text-slate-900 uppercase text-[11px]" id="succ-account-name">{{ $accountName }}</span>
                    </div>

                    <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Số tiền:</span>
                        <div class="flex items-center space-x-1.5">
                            <span class="font-mono font-black text-rose-600 text-sm" id="succ-transfer-amount">0₫</span>
                            <button type="button" onclick="copyRawAmount()" class="px-2 py-0.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-md text-[10px] font-bold border border-rose-200 transition">
                                Sao chép
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Nội dung chuyển:</span>
                        <div class="flex items-center space-x-1.5">
                            <span class="font-mono font-black text-emerald-700 text-sm" id="succ-transfer-content">ORD-XXXX</span>
                            <button type="button" onclick="copyText(document.getElementById('succ-transfer-content').textContent, 'Đã sao chép nội dung chuyển khoản!')" class="px-2 py-0.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-md text-[10px] font-bold border border-emerald-200 transition">
                                Sao chép
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Button: Download QR Image -->
                <button type="button" onclick="downloadQrImage()" class="w-full py-2.5 px-4 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5">
                    <span>📥</span>
                    <span>Tải ảnh mã QR về máy để quét trong App</span>
                </button>

                <p class="text-[10px] text-slate-500 leading-tight">
                    💡 Quý khách giữ nguyên nội dung chuyển khoản để hệ thống xác nhận thanh toán tự động trong 3 giây.
                </p>
                </div>
            </div>

            <div class="pt-2 flex flex-col sm:flex-row gap-2">
                <a id="succ-print-btn" href="#" target="_blank" class="hidden flex-1 py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition text-center flex items-center justify-center space-x-1.5">
                    <span>🖨️</span>
                    <span>In Hóa Đơn</span>
                </a>
                <a href="{{ route('storefront.products') }}" class="flex-1 py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition text-center">
                    Tiếp Tục Mua Sắm
                </a>
                <a href="{{ route('storefront.index') }}" class="flex-1 py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition text-center">
                    Về Trang Chủ
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const CHECKOUT_CONFIG = {
        freeshipLimit: {{ (int) $freeshipThreshold }},
        shippingFeeDefault: {{ (int) $shippingFeeDefault }},
        vatRate: {{ (float) $defaultVatRate }},
        bankCode: @json($bankCode),
        bankName: @json($bankName),
        accountNumber: @json($accountNumber),
        accountName: @json($accountName),
    };

    window._currentOrderGrandTotal = 0;

    function renderCheckoutItems() {
        const cart = getCart();
        const emptyState = document.getElementById('checkout-empty-cart-state');
        const activeSection = document.getElementById('checkout-active-section');
        const container = document.getElementById('checkout-items-list');

        if (!cart || cart.length === 0) {
            if (emptyState) emptyState.classList.remove('hidden');
            if (activeSection) activeSection.classList.add('hidden');
            return;
        }

        if (emptyState) emptyState.classList.add('hidden');
        if (activeSection) activeSection.classList.remove('hidden');

        let html = '';
        let subtotal = 0;
        let count = 0;

        cart.forEach((item, index) => {
            const itemTotal = item.quantity * item.price;
            subtotal += itemTotal;
            count += item.quantity;

            html += `
                <div class="pt-3 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-3 max-w-[65%]">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0 border border-slate-200">
                            ${item.image_url ? `<img src="${item.image_url}" class="max-h-full object-contain p-1" />` : `<span>📦</span>`}
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 line-clamp-1">${item.name}</h4>
                            <span class="text-[10px] text-slate-400 font-semibold">${item.unit_name || 'Cái'} • ${formatMoney(item.price)}</span>
                        </div>
                    </div>
                    <div class="flex flex-col items-end space-y-1">
                        <span class="font-mono font-bold text-slate-900">${formatMoney(itemTotal)}</span>
                        <div class="flex items-center space-x-1 bg-slate-100 rounded-lg p-0.5">
                            <button type="button" onclick="updateItemQuantity(${index}, -1)" class="w-5 h-5 flex items-center justify-center bg-white rounded font-bold hover:bg-slate-200">-</button>
                            <span class="w-6 text-center font-bold font-mono">${item.quantity}</span>
                            <button type="button" onclick="updateItemQuantity(${index}, 1)" class="w-5 h-5 flex items-center justify-center bg-white rounded font-bold hover:bg-slate-200">+</button>
                        </div>
                        <button type="button" onclick="removeCheckoutItem(${index})" class="text-[10px] text-rose-500 hover:underline">Xóa</button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;

        // Dynamic Shipping Fee based on Setting
        const freeshipLimit = CHECKOUT_CONFIG.freeshipLimit;
        const shippingFee = (subtotal >= freeshipLimit || subtotal === 0) ? 0 : CHECKOUT_CONFIG.shippingFeeDefault;

        // Freeship Progress Banner
        const freeshipBanner = document.getElementById('chk-freeship-banner');
        const freeshipText = document.getElementById('chk-freeship-text');
        const freeshipBar = document.getElementById('chk-freeship-bar');

        if (freeshipBanner && freeshipText && freeshipBar) {
            if (subtotal > 0) {
                freeshipBanner.classList.remove('hidden');
                if (subtotal >= freeshipLimit) {
                    freeshipBanner.className = 'p-3 rounded-2xl border text-xs space-y-1.5 transition bg-emerald-50 border-emerald-200 text-emerald-800';
                    freeshipText.innerHTML = '<span class="font-bold flex items-center space-x-1"><span>🎉</span><span>Chúc mừng! Bạn được MIỄN PHÍ VẬN CHUYỂN</span></span>';
                    freeshipBar.style.width = '100%';
                    freeshipBar.className = 'h-1.5 rounded-full bg-emerald-500 transition-all duration-300';
                } else {
                    const diff = freeshipLimit - subtotal;
                    const percent = Math.min(100, Math.round((subtotal / freeshipLimit) * 100));
                    freeshipBanner.className = 'p-3 rounded-2xl border text-xs space-y-1.5 transition bg-indigo-50/70 border-indigo-200 text-slate-700';
                    freeshipText.innerHTML = `<span>Mua thêm <b class="text-indigo-700 font-mono">${formatMoney(diff)}</b> để được <b>Freeship</b></span><span class="font-mono font-bold text-slate-400 text-[10px]">${percent}%</span>`;
                    freeshipBar.style.width = percent + '%';
                    freeshipBar.className = 'h-1.5 rounded-full bg-indigo-600 transition-all duration-300';
                }
            } else {
                freeshipBanner.classList.add('hidden');
            }
        }

        // Dynamic VAT Real-Time Calculation
        const invoiceCheckbox = document.querySelector('[data-invoice-enabled]');
        const isVat = invoiceCheckbox && invoiceCheckbox.checked;
        let vatAmount = 0;
        const vatRow = document.getElementById('chk-vat-row');
        const vatVal = document.getElementById('chk-vat-val');

        if (isVat && subtotal > 0) {
            vatAmount = Math.round(subtotal * (CHECKOUT_CONFIG.vatRate / 100));
            if (vatRow) vatRow.classList.remove('hidden');
            if (vatVal) vatVal.textContent = formatMoney(vatAmount);
        } else {
            if (vatRow) vatRow.classList.add('hidden');
        }

        const grandTotal = subtotal + shippingFee + vatAmount;

        document.getElementById('checkout-total-items-badge').textContent = count + ' món';
        document.getElementById('chk-subtotal-val').textContent = formatMoney(subtotal);
        const shippingEl = document.getElementById('chk-shipping-val');
        if (shippingEl) {
            shippingEl.textContent = shippingFee === 0 ? 'Miễn phí giao hàng' : formatMoney(shippingFee);
        }
        document.getElementById('chk-grand-total-val').textContent = formatMoney(grandTotal);
    }

    function updateItemQuantity(index, delta) {
        let cart = getCart();
        if (cart[index]) {
            cart[index].quantity += delta;
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            saveCart(cart);
            renderCheckoutItems();
        }
    }

    function removeCheckoutItem(index) {
        let cart = getCart();
        cart.splice(index, 1);
        saveCart(cart);
        renderCheckoutItems();
    }

    function onPaymentChange(radio) {
        const labels = document.querySelectorAll('.payment-radio-label');
        labels.forEach(l => {
            const r = l.querySelector('input');
            if (r.checked) {
                l.classList.add('border-indigo-600', 'bg-indigo-50/40');
                l.classList.remove('border-slate-200', 'bg-white');
            } else {
                l.classList.remove('border-indigo-600', 'bg-indigo-50/40');
                l.classList.add('border-slate-200', 'bg-white');
            }
        });

        const isVietQr = document.querySelector('input[name="payment_method"]:checked')?.value === 'vietqr';
        const hint = document.getElementById('vietqr-bank-hint');
        if (hint) {
            if (isVietQr) hint.classList.remove('hidden');
            else hint.classList.add('hidden');
        }
    }

    async function copyText(text, successMsg = 'Đã sao chép vào bộ nhớ tạm!') {
        try {
            await navigator.clipboard.writeText(text.trim());
            if (typeof showToast === 'function') {
                showToast(successMsg, 'success');
            } else {
                alert(successMsg);
            }
        } catch (e) {
            const temp = document.createElement('textarea');
            temp.value = text.trim();
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
            if (typeof showToast === 'function') {
                showToast(successMsg, 'success');
            } else {
                alert(successMsg);
            }
        }
    }

    function copyRawAmount() {
        const raw = window._currentOrderGrandTotal || 0;
        copyText(raw.toString(), 'Đã sao chép số tiền cần chuyển!');
    }

    function downloadQrImage() {
        const img = document.getElementById('succ-vietqr-img');
        const orderCode = document.getElementById('succ-order-code').textContent.trim() || 'don-hang';
        if (!img || !img.src) return;
        const a = document.createElement('a');
        a.href = img.src;
        a.download = `vietqr-${orderCode}.png`;
        a.target = '_blank';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    let _paymentPollingTimer = null;
    let _paymentPollingCount = 0;

    function stopPaymentPolling() {
        if (_paymentPollingTimer) {
            clearInterval(_paymentPollingTimer);
            _paymentPollingTimer = null;
        }
    }

    function startPaymentPolling(orderCode) {
        stopPaymentPolling();
        _paymentPollingCount = 0;
        const maxPolls = 100; // Kiểm tra mỗi 3s trong 5 phút

        _paymentPollingTimer = setInterval(async () => {
            _paymentPollingCount++;
            if (_paymentPollingCount > maxPolls) {
                stopPaymentPolling();
                return;
            }

            try {
                const res = await fetch(`/don-hang/kiem-tra-thanh-toan/${encodeURIComponent(orderCode)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const resData = await res.json();
                if (resData.is_paid) {
                    stopPaymentPolling();
                    onPaymentSuccess(resData);
                }
            } catch (e) {
                console.error('Lỗi kiểm tra trạng thái thanh toán:', e);
            }
        }, 3000);
    }

    function onPaymentSuccess(data) {
        const waitingBox = document.getElementById('succ-status-waiting');
        const paidBox = document.getElementById('succ-status-paid');
        const qrMedia = document.getElementById('succ-qr-media-container');

        if (waitingBox) waitingBox.classList.add('hidden');
        if (paidBox) paidBox.classList.remove('hidden');
        if (qrMedia) qrMedia.classList.add('hidden');

        if (typeof showToast === 'function') {
            showToast('🎉 Đã nhận thanh toán VietQR thành công! Đơn hàng đang được chuẩn bị.', 'success');
        }
    }

    async function submitCheckoutPage(e) {
        e.preventDefault();
        const checkoutForm = document.getElementById('main-checkout-form');
        if (!window.CheckoutValidation.validate(checkoutForm)) return;
        const cart = getCart();
        if (cart.length === 0) {
            alert('Giỏ hàng của bạn đang trống!');
            return;
        }

        const name = document.getElementById('chk-name').value.trim();
        const phone = document.getElementById('chk-phone').value.trim();
        const address = document.getElementById('chk-address').value.trim();
        const notes = document.getElementById('chk-notes').value.trim();
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cod';

        const websiteUrl = document.getElementById('chk-website-url')?.value || '';

        const btn = document.getElementById('btn-submit-checkout');
        const errBox = document.getElementById('checkout-error-alert');
        errBox.classList.add('hidden');
        btn.disabled = true;
        btn.innerHTML = '<span>Đang tạo đơn hàng...</span>';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            const response = await fetch("{{ route('storefront.checkout') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    customer_name: name,
                    customer_phone: phone,
                    customer_address: address,
                    payment_method: paymentMethod,
                    notes: notes,
                    website_url: websiteUrl,
                    ...window.InvoiceForm.read('checkout-invoice'),
                    items: cart.map(i => ({
                        product_id: i.product_id,
                        unit_id: i.unit_id,
                        quantity: i.quantity,
                    })),
                }),
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                const validationMessage = window.CheckoutValidation.serverErrors(checkoutForm, data.errors, errBox);
                throw new Error(validationMessage || data.message || 'Không thể tạo đơn hàng, vui lòng kiểm tra lại thông tin.');
            }

            // Success! Save grand total and clear cart
            window._currentOrderGrandTotal = data.grand_total || 0;
            saveCart([]);

            // Populate Success Modal
            document.getElementById('succ-order-code').textContent = data.order_code;
            document.getElementById('succ-order-total').textContent = data.grand_total_formatted;

            // In / Xem Hóa Đơn Button
            const printBtn = document.getElementById('succ-print-btn');
            if (printBtn) {
                if (data.order_id) {
                    printBtn.href = `/print/order/${data.order_id}`;
                    printBtn.classList.remove('hidden');
                } else {
                    printBtn.classList.add('hidden');
                }
            }

            const qrBox = document.getElementById('succ-vietqr-box');
            const qrImg = document.getElementById('succ-vietqr-img');
            const waitingBox = document.getElementById('succ-status-waiting');
            const paidBox = document.getElementById('succ-status-paid');
            const qrMedia = document.getElementById('succ-qr-media-container');

            if (data.viet_qr_url && paymentMethod === 'vietqr') {
                qrImg.src = data.viet_qr_url;
                if (data.bank_name) document.getElementById('succ-bank-name').textContent = data.bank_name;
                if (data.account_number) document.getElementById('succ-account-number').textContent = data.account_number;
                if (data.account_name) document.getElementById('succ-account-name').textContent = data.account_name;
                if (data.grand_total_formatted) document.getElementById('succ-transfer-amount').textContent = data.grand_total_formatted;
                if (data.transfer_content) document.getElementById('succ-transfer-content').textContent = data.transfer_content;

                if (waitingBox) waitingBox.classList.remove('hidden');
                if (paidBox) paidBox.classList.add('hidden');
                if (qrMedia) qrMedia.classList.remove('hidden');

                qrBox.classList.remove('hidden');

                // Bắt đầu kiểm tra thanh toán thời gian thực
                startPaymentPolling(data.order_code);
            } else {
                stopPaymentPolling();
                qrBox.classList.add('hidden');
            }

            document.getElementById('checkout-success-modal').classList.remove('hidden');

        } catch (err) {
            errBox.textContent = err.message;
            errBox.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<span>XÁC NHẬN ĐẶT HÀNG NGAY</span><span>→</span>';
        }
    }

    // Gắn sự kiện tính lại thuế khi bật/tắt checkbox hóa đơn VAT
    function setupInvoiceListener() {
        const invoiceCheckbox = document.querySelector('[data-invoice-enabled]');
        if (invoiceCheckbox) {
            invoiceCheckbox.addEventListener('change', renderCheckoutItems);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            renderCheckoutItems();
            setupInvoiceListener();
        });
    } else {
        renderCheckoutItems();
        setupInvoiceListener();
    }
</script>
@endpush
