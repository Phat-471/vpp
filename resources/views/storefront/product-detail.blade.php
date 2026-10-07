@extends('layouts.storefront')

@section('title', $product->name . ' | VPP & Dịch Vụ Máy In')
@section('meta_description', 'Mua ' . $product->name . ' chính hãng giá sỉ tốt nhất. SKU: ' . $product->sku . '. Giao nhanh 2h, xuất hóa đơn VAT điện tử.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">

    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs text-slate-500 font-medium overflow-x-auto no-scrollbar">
        <a href="{{ route('storefront.index') }}" class="hover:text-indigo-600 whitespace-nowrap">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('storefront.products') }}" class="hover:text-indigo-600 whitespace-nowrap">Sản phẩm</a>
        @if($product->category)
        <span>/</span>
        <a href="{{ route('storefront.products', ['category' => $product->category->slug]) }}" class="hover:text-indigo-600 whitespace-nowrap">
            {{ $product->category->name }}
        </a>
        @endif
        <span>/</span>
        <span class="text-slate-900 font-bold truncate">{{ $product->name }}</span>
    </nav>

    <!-- Main Product Section: Gallery (5 cols) + Details (7 cols) -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-xs grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Product Image Gallery (lg:col-span-5) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="relative w-full aspect-square bg-slate-50 rounded-3xl border border-slate-200 overflow-hidden flex items-center justify-center p-6 group">
                @if(!empty($product->featured_image_large) || !empty($product->image_url))
                <img src="{{ $product->featured_image_large ?: $product->image_url }}" alt="{{ $product->name }}" class="max-w-full max-h-full object-contain group-hover:scale-105 transition transform duration-300" id="main-product-img" />
                @else
                <div class="text-6xl text-slate-300">
                    @if(str_contains($product->category?->slug ?? '', 'giay')) 📄
                    @elseif(str_contains($product->category?->slug ?? '', 'but')) ✏️
                    @elseif(str_contains($product->category?->slug ?? '', 'muc')) 🖨️
                    @elseif(str_contains($product->category?->slug ?? '', 'bia')) 📁
                    @else 📦
                    @endif
                </div>
                @endif

                <span class="absolute top-4 left-4 px-3 py-1 bg-indigo-600 text-white font-bold text-xs rounded-full shadow-xs">
                    CHÍNH HÃNG 100%
                </span>
            </div>

            <!-- Trust Badges Under Image -->
            <div class="grid grid-cols-3 gap-2 text-center text-[11px] pt-1">
                <div class="p-2 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="block font-bold text-indigo-700">🚚 Giao 2 Giờ</span>
                    <span class="text-slate-400">Nội thành TP.HCM</span>
                </div>
                <div class="p-2 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="block font-bold text-emerald-600">🔄 Đổi Trả 7 Ngày</span>
                    <span class="text-slate-400">Lỗi nhà sản xuất</span>
                </div>
                <div class="p-2 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="block font-bold text-amber-600">🧾 Xuất VAT</span>
                    <span class="text-slate-400">Hóa đơn điện tử</span>
                </div>
            </div>
        </div>

        <!-- Product Details & Purchase Controls (lg:col-span-7) -->
        <div class="lg:col-span-7 space-y-6">
            
            <div class="space-y-2 border-b border-slate-100 pb-4">
                <div class="flex items-center space-x-3 text-xs">
                    <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 rounded-md font-bold uppercase tracking-wider">
                        {{ $product->category?->name ?? 'Văn Phòng Phẩm' }}
                    </span>
                    <span class="text-slate-400">|</span>
                    <span class="font-mono text-slate-500 font-semibold">SKU: {{ $product->sku }}</span>
                    @if($product->barcode)
                    <span class="text-slate-400">|</span>
                    <span class="font-mono text-slate-500">Mã vạch: {{ $product->barcode }}</span>
                    @endif
                </div>

                <h1 class="text-xl sm:text-3xl font-black text-slate-900 leading-tight">
                    {{ $product->name }}
                </h1>

                <!-- Stock status -->
                <div class="flex items-center space-x-2 pt-1 text-xs">
                    <span class="inline-block w-2.5 h-2.5 rounded-full {{ $product->stock_quantity > 0 ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                    <span class="font-bold {{ $product->stock_quantity > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                        {{ $product->stock_quantity > 0 ? 'Tình trạng: Còn hàng trong kho (' . $product->stock_quantity . ' ' . $product->base_unit . ')' : 'Tình trạng: Tạm hết hàng' }}
                    </span>
                </div>
            </div>

            <!-- Price Box -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="flex items-baseline space-x-3">
                    <span class="text-2xl sm:text-3xl font-black text-indigo-700 font-mono" id="current-display-price">
                        {{ number_format($product->retail_price, 0, ',', '.') }}₫
                    </span>
                    <span class="text-xs text-slate-500 font-medium" id="current-display-unit">
                        / 1 {{ $product->base_unit }} (Đơn vị cơ sở)
                    </span>
                </div>
                <p class="text-[11px] text-slate-500">
                    Đã bao gồm thuế giá trị gia tăng (VAT). Khách hàng có thể chọn mua theo đơn vị quy đổi bên dưới để được hưởng giá sỉ.
                </p>
            </div>

            <!-- Unit Selection (Dual Units: Bán lẻ vs Bán Thùng) -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                    Chọn Quy Cách Đóng Gói:
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="unit-selection-container">
                    
                    <!-- Base unit -->
                    <label class="relative flex items-center justify-between p-3.5 rounded-2xl border-2 border-indigo-600 bg-indigo-50/40 cursor-pointer transition">
                        <input type="radio" name="selected_unit" value="" checked 
                               data-unit-id="" 
                               data-unit-name="{{ $product->base_unit }}" 
                               data-price="{{ (float) $product->retail_price }}"
                               onchange="onUnitChanged(this)"
                               class="accent-indigo-600 w-4 h-4 mr-2" />
                        <div class="flex-1">
                            <span class="block text-xs font-bold text-slate-900">Bán Lẻ: 1 {{ $product->base_unit }}</span>
                            <span class="text-[11px] text-slate-500">Quy cách tiêu chuẩn</span>
                        </div>
                        <span class="font-mono font-bold text-indigo-700 text-xs sm:text-sm">
                            {{ number_format($product->retail_price, 0, ',', '.') }}₫
                        </span>
                    </label>

                    <!-- Dual Unit (if available) -->
                    @foreach($product->units as $u)
                    <label class="relative flex items-center justify-between p-3.5 rounded-2xl border-2 border-slate-200 hover:border-indigo-400 bg-white cursor-pointer transition">
                        <input type="radio" name="selected_unit" value="{{ $u->id }}" 
                               data-unit-id="{{ $u->id }}" 
                               data-unit-name="{{ $u->unit_name }}" 
                               data-price="{{ (float) $u->price }}"
                               onchange="onUnitChanged(this)"
                               class="accent-indigo-600 w-4 h-4 mr-2" />
                        <div class="flex-1">
                            <span class="block text-xs font-bold text-slate-900">Bán Sỉ: 1 {{ $u->unit_name }} (x{{ $u->conversion_rate }})</span>
                            <span class="text-[11px] text-emerald-600 font-semibold">Tiết kiệm giá sỉ</span>
                        </div>
                        <span class="font-mono font-bold text-indigo-700 text-xs sm:text-sm">
                            {{ number_format($u->price, 0, ',', '.') }}₫
                        </span>
                    </label>
                    @endforeach

                </div>
            </div>

            <!-- Quantity & Actions -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center space-x-4">
                    <label class="text-xs font-bold text-slate-700 uppercase">Số Lượng:</label>
                    <div class="flex items-center space-x-1 bg-slate-100 rounded-xl p-1 border border-slate-200">
                        <button type="button" onclick="changeDetailQty(-1)" class="w-8 h-8 rounded-lg bg-white hover:bg-slate-200 text-slate-700 font-black text-sm flex items-center justify-center transition">-</button>
                        <input type="number" id="detail-qty-input" value="1" min="1" max="999" class="w-12 text-center bg-transparent text-sm font-bold font-mono focus:outline-none" />
                        <button type="button" onclick="changeDetailQty(1)" class="w-8 h-8 rounded-lg bg-white hover:bg-slate-200 text-slate-700 font-black text-sm flex items-center justify-center transition">+</button>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button
                        type="button"
                        onclick="submitAddToCart()"
                        class="flex-1 py-3.5 px-6 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border-2 border-indigo-600 font-black text-xs sm:text-sm shadow-xs transition flex items-center justify-center space-x-2"
                    >
                        <span>🛒</span>
                        <span>THÊM VÀO GIỎ HÀNG</span>
                    </button>

                    <button
                        type="button"
                        onclick="submitBuyNow()"
                        class="flex-1 py-3.5 px-6 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs sm:text-sm shadow-md transition transform hover:-translate-y-0.5 flex items-center justify-center space-x-2"
                    >
                        <span>⚡</span>
                        <span>MUA NGAY (THANH TOÁN)</span>
                    </button>
                </div>
            </div>

            <!-- Compatible Printers (If toner cartridge or parts) -->
            @if($product->compatiblePrinters->count() > 0)
            <div class="pt-4 border-t border-slate-100 space-y-2">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-1.5">
                    <span>🖨️</span>
                    <span>Tương Thích Tuyệt Đối Với Các Dòng Máy In:</span>
                </h4>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($product->compatiblePrinters as $cp)
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-xs font-semibold">
                        {{ $cp->brand }} {{ $cp->model_name }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

    </div>

    <!-- Product Description & Specifications -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-xs space-y-6">
        <h2 class="text-lg sm:text-xl font-black text-slate-900 border-b border-slate-100 pb-3">
            Mô Tả & Thông Tin Chi Tiết Sản Phẩm
        </h2>

        <div class="prose max-w-none text-xs sm:text-sm text-slate-700 leading-relaxed space-y-4">
            @if(!empty($product->description))
            <p>{{ $product->description }}</p>
            @else
            <p>
                Sản phẩm <b>{{ $product->name }}</b> (Mã SKU: <b>{{ $product->sku }}</b>) thuộc danh mục <b>{{ $product->category?->name ?? 'Văn Phòng Phẩm' }}</b> chính hãng, được phân phối và bảo hành trực tiếp bởi Cửa hàng Văn Phòng Phẩm & Dịch Vụ Máy In.
            </p>
            @endif

            <div class="bg-slate-50 p-4 sm:p-6 rounded-2xl border border-slate-200/80 space-y-2">
                <h3 class="font-bold text-slate-900 text-sm">Chính Sách Bán Hàng & Hậu Mãi:</h3>
                <ul class="list-disc pl-5 space-y-1.5 text-xs text-slate-600">
                    <li><b>Đồng kiểm khi nhận hàng:</b> Quý khách được quyền mở kiện hàng kiểm tra đúng chủng loại và số lượng trước khi thanh toán.</li>
                    <li><b>Giao hàng hỏa tốc:</b> Đơn hàng nội thành TP.HCM được xử lý giao trong 2 giờ. Miễn phí ship cho đơn từ 500.000₫.</li>
                    <li><b>Hóa đơn điện tử VAT:</b> Cửa hàng hỗ trợ xuất hóa đơn GTGT đầy đủ cho công ty, doanh nghiệp trong ngày.</li>
                    <li><b>Đổi trả 1-đổi-1:</b> Trong vòng 7 ngày nếu phát hiện lỗi kỹ thuật từ nhà sản xuất.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-black text-slate-900">Sản Phẩm Cùng Ngành Hàng Gợi Ý</h3>
            <a href="{{ route('storefront.products', ['category' => $product->category?->slug]) }}" class="text-xs text-indigo-600 font-bold hover:underline">Xem thêm →</a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($relatedProducts as $rp)
            <div class="bg-white rounded-2xl border border-slate-200 p-3 flex flex-col justify-between hover:shadow-md transition">
                <a href="{{ route('storefront.product-detail', $rp->slug) }}" class="block">
                    <div class="aspect-square bg-slate-50 rounded-xl mb-2 flex items-center justify-center p-2">
                        @if(!empty($rp->featured_image_thumb))
                        <img src="{{ $rp->featured_image_thumb }}" alt="{{ $rp->name }}" class="max-h-full object-contain" />
                        @else
                        <span class="text-2xl">📦</span>
                        @endif
                    </div>
                    <h4 class="font-bold text-xs text-slate-900 line-clamp-2 mb-1 hover:text-indigo-600 transition">{{ $rp->name }}</h4>
                </a>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                    <span class="font-mono font-bold text-indigo-700 text-xs">{{ number_format($rp->retail_price, 0, ',', '.') }}₫</span>
                    <a href="{{ route('storefront.product-detail', $rp->slug) }}" class="text-[10px] text-slate-500 hover:text-indigo-600 font-bold">Xem →</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    let currentUnitId = null;
    let currentUnitName = '{{ $product->base_unit }}';
    let currentUnitPrice = {{ (float) $product->retail_price }};

    function onUnitChanged(radio) {
        currentUnitId = radio.dataset.unitId || null;
        currentUnitName = radio.dataset.unitName;
        currentUnitPrice = parseFloat(radio.dataset.price);

        document.getElementById('current-display-price').textContent = formatMoney(currentUnitPrice);
        document.getElementById('current-display-unit').textContent = '/ 1 ' + currentUnitName;

        // Highlight selected border
        const container = document.getElementById('unit-selection-container');
        const labels = container.querySelectorAll('label');
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
    }

    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
    }

    function changeDetailQty(delta) {
        const input = document.getElementById('detail-qty-input');
        let val = parseInt(input.value) || 1;
        val += delta;
        if (val < 1) val = 1;
        if (val > 999) val = 999;
        input.value = val;
    }

    function submitAddToCart() {
        const qty = parseInt(document.getElementById('detail-qty-input').value) || 1;
        let cart = getCart();
        const key = '{{ $product->id }}' + '_' + (currentUnitId || 'base');
        const existing = cart.find(i => i.key === key);

        if (existing) {
            existing.quantity += qty;
        } else {
            cart.push({
                key: key,
                product_id: {{ $product->id }},
                unit_id: currentUnitId,
                name: '{{ addslashes($product->name) }}',
                unit_name: currentUnitName,
                price: currentUnitPrice,
                image_url: '{{ $product->image_url }}',
                quantity: qty
            });
        }

        saveCart(cart);
        showToast('Đã thêm ' + qty + ' ' + currentUnitName + ' vào giỏ hàng!');
    }

    function submitBuyNow() {
        submitAddToCart();
        window.location.href = "{{ route('storefront.checkout-page') }}";
    }
</script>
@endpush
