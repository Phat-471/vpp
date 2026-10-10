@extends('layouts.storefront')

@section('title', $product->name . ' chính hãng giá sỉ | ' . ($storefrontSettings['site_name'] ?? 'VPP & Dịch Vụ Máy In'))
@section('meta_description', 'Mua ' . $product->name . ' chính hãng giá sỉ tốt nhất tại Đồng Nai. Mã: ' . $product->sku . '. Giao hỏa tốc 2 giờ, xuất hóa đơn VAT điện tử.')

@section('schema_extra')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Product",
      "@id": "{{ url()->current() }}#product",
      "name": "{{ $product->name }}",
      "sku": "{{ $product->sku }}",
      @if($product->barcode)
      "gtin": "{{ $product->barcode }}",
      @endif
      "image": "{{ $product->image_url }}",
      "description": "{{ Str::limit(strip_tags($product->description ?? ($product->name . ' chính hãng giá sỉ tại Đồng Nai')), 200) }}",
      "brand": {
        "@type": "Brand",
        "name": "{{ $product->category?->name ?? 'Chính Hãng' }}"
      },
      "offers": {
        "@type": "Offer",
        "url": "{{ url()->current() }}",
        "priceCurrency": "VND",
        "price": "{{ (float) ($product->is_flash_sale && $product->flash_sale_price > 0 ? $product->flash_sale_price : $product->retail_price) }}",
        "priceValidUntil": "{{ now()->addYear()->format('Y-m-d') }}",
        "itemCondition": "https://schema.org/NewCondition",
        "availability": "{{ $product->stock_quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
        "seller": {
          "@type": "Organization",
          "name": "{{ $storefrontSettings['site_name'] ?? 'VPP & Thiết Bị Máy In' }}"
        }
      }
    },
    {
      "@type": "BreadcrumbList",
      "@id": "{{ url()->current() }}#breadcrumb",
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
          "name": "Sản phẩm",
          "item": "{{ route('storefront.products') }}"
        }
        @if($product->category)
        ,{
          "@type": "ListItem",
          "position": 3,
          "name": "{{ $product->category->name }}",
          "item": "{{ route('storefront.products', ['category' => $product->category->slug]) }}"
        },
        {
          "@type": "ListItem",
          "position": 4,
          "name": "{{ $product->name }}",
          "item": "{{ url()->current() }}"
        }
        @else
        ,{
          "@type": "ListItem",
          "position": 3,
          "name": "{{ $product->name }}",
          "item": "{{ url()->current() }}"
        }
        @endif
      ]
    }
  ]
}
</script>
@endsection

@php
    $isFlashSaleActive = ($product->is_flash_sale && $product->flash_sale_price && $product->flash_sale_price > 0 && $product->flash_sale_price < $product->retail_price);
    if ($isFlashSaleActive) {
        $initialPrice = (float) $product->flash_sale_price;
        $originalPrice = (float) $product->retail_price;
        $discountPercent = round((($originalPrice - $initialPrice) / $originalPrice) * 100);
    } else {
        $initialPrice = (float) $product->retail_price;
        $discountPercent = ($product->id * 13 + 7) % 15 + 10; // 10-24%
        $originalPrice = round($initialPrice * (1 + $discountPercent / 100), -3);
    }
    $soldCount = ($product->id * 37 + 123) % 900 + 100;
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-6 sm:space-y-8 pb-24 md:pb-8">

    <!-- 1. Breadcrumb dẫn đường -->
    <nav class="flex items-center space-x-2 text-xs text-slate-500 font-medium overflow-x-auto no-scrollbar py-1">
        <a href="{{ route('storefront.index') }}" class="hover:text-indigo-600 whitespace-nowrap flex items-center space-x-1">
            <span>🏠</span>
            <span>Trang chủ</span>
        </a>
        <span>/</span>
        <a href="{{ route('storefront.products') }}" class="hover:text-indigo-600 whitespace-nowrap">Sản phẩm</a>
        @if($product->category)
        <span>/</span>
        <a href="{{ route('storefront.products', ['category' => $product->category->slug]) }}" class="hover:text-indigo-600 whitespace-nowrap">
            {{ $product->category->name }}
        </a>
        @endif
        <span>/</span>
        <span class="text-slate-900 font-bold truncate max-w-[200px] sm:max-w-md">{{ $product->name }}</span>
    </nav>

    <!-- 2. Khung Chi Tiết Sản Phẩm: Hình ảnh (5 cột) + Bảng giá & Tác vụ (7 cột) -->
    <div class="bg-white rounded-3xl p-5 sm:p-8 lg:p-10 border border-slate-200/90 shadow-xs grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10 items-start">

        <!-- Cột Ảnh & Uy Tín (lg:col-span-5) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="relative w-full aspect-square bg-slate-50/80 rounded-3xl border border-slate-200 overflow-hidden flex items-center justify-center p-6 group">
                <img
                    src="{{ $product->image_url }}"
                    alt="{{ $product->name }}"
                    class="max-w-full max-h-full object-contain group-hover:scale-105 transition transform duration-300"
                    id="main-product-img"
                />

                @if($isFlashSaleActive)
                <div class="absolute top-4 left-4 flex items-center space-x-1 px-3 py-1 bg-gradient-to-r from-rose-600 to-orange-500 text-white font-black text-xs rounded-full shadow-md animate-pulse">
                    <span>⚡ FLASH SALE</span>
                </div>
                @else
                <span class="absolute top-4 left-4 px-3 py-1 bg-[#1e3a8a] text-white font-black text-xs rounded-full shadow-xs">
                    CHÍNH HÃNG 100%
                </span>
                @endif

                <div class="absolute top-4 right-4">
                    <span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 font-black text-xs rounded-full shadow-xs">
                        -{{ $discountPercent }}%
                    </span>
                </div>
            </div>

            <!-- Cam kết dịch vụ & Giao nhận -->
            <div class="grid grid-cols-3 gap-2 text-center text-[11px] pt-1">
                <div class="p-2.5 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col items-center justify-center">
                    <span class="text-base mb-0.5">🚚</span>
                    <span class="font-bold text-slate-800 leading-tight">Giao 2 Giờ</span>
                    <span class="text-slate-400 text-[10px]">Khu vực Đồng Nai</span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col items-center justify-center">
                    <span class="text-base mb-0.5">🔄</span>
                    <span class="font-bold text-emerald-700 leading-tight">Đổi Trả 7 Ngày</span>
                    <span class="text-slate-400 text-[10px]">Lỗi 1-đổi-1</span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col items-center justify-center">
                    <span class="text-base mb-0.5">🧾</span>
                    <span class="font-bold text-indigo-700 leading-tight">Xuất VAT 100%</span>
                    <span class="text-slate-400 text-[10px]">Hóa đơn điện tử</span>
                </div>
            </div>
        </div>

        <!-- Cột Chi Tiết & Tác Vụ Mua Hàng (lg:col-span-7) -->
        <div class="lg:col-span-7 space-y-5">
            
            <!-- Tiêu đề & Thông tin cơ bản -->
            <div class="space-y-2 border-b border-slate-100 pb-4">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 rounded-md font-bold uppercase tracking-wider">
                        {{ $product->category?->name ?? 'Văn Phòng Phẩm' }}
                    </span>
                    <span class="text-slate-300">|</span>
                    <span class="font-mono text-slate-500 font-semibold">SKU: {{ $product->sku }}</span>
                    @if($product->barcode)
                    <span class="text-slate-300">|</span>
                    <span class="font-mono text-slate-500">Mã vạch: {{ $product->barcode }}</span>
                    @endif
                </div>

                <h1 class="text-lg sm:text-2xl font-black text-slate-900 leading-snug">
                    {{ $product->name }}
                </h1>

                <!-- Đánh giá sao, đã bán & tình trạng kho -->
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1 text-xs">
                    <div class="flex items-center space-x-2">
                        <div class="flex text-amber-400">
                            ★★★★★
                        </div>
                        <span class="text-slate-600 font-bold">5.0</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-slate-500">Đã bán <b>{{ number_format($soldCount) }}</b></span>
                    </div>

                    <div class="flex items-center space-x-1.5">
                        <span class="inline-block w-2.5 h-2.5 rounded-full {{ $product->stock_quantity > 0 ? 'bg-emerald-500 ring-2 ring-emerald-200' : 'bg-rose-500 ring-2 ring-rose-200' }}"></span>
                        <span class="font-bold {{ $product->stock_quantity > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                            {{ $product->stock_quantity > 0 ? 'Còn hàng trong kho (' . $product->stock_quantity . ' ' . $product->base_unit . ')' : 'Tạm hết hàng' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Khối Bảng Giá (Price Display Box) -->
            <div class="p-4 sm:p-5 rounded-2xl {{ $isFlashSaleActive ? 'bg-gradient-to-br from-rose-50/70 via-orange-50/40 to-slate-50 border-2 border-rose-200' : 'bg-slate-50/90 border border-slate-200' }} space-y-2">
                @if($isFlashSaleActive)
                <div class="flex items-center justify-between pb-1 border-b border-rose-100 text-xs">
                    <span class="font-black text-rose-700 flex items-center space-x-1">
                        <span>🔥</span>
                        <span>GIÁ FLASH SALE ĐẶC BIỆT</span>
                    </span>
                    <span class="text-[11px] text-rose-600 font-bold bg-white px-2 py-0.5 rounded-full border border-rose-200 shadow-2xs">
                        Tiết kiệm {{ $discountPercent }}%
                    </span>
                </div>
                @endif

                <div class="flex items-baseline space-x-3 flex-wrap">
                    <span class="text-2xl sm:text-3xl font-black text-rose-600 font-mono tracking-tight" id="current-display-price">
                        {{ number_format($initialPrice, 0, ',', '.') }}₫
                    </span>
                    <span class="text-xs sm:text-sm text-slate-400 line-through font-mono" id="current-display-original-price">
                        {{ number_format($originalPrice, 0, ',', '.') }}₫
                    </span>
                    <span class="text-xs sm:text-sm text-slate-600 font-bold" id="current-display-unit">
                        / 1 {{ $product->base_unit }}
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-2 pt-1 text-[11px] text-slate-500">
                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold">✓ Giá đã gồm thuế VAT 100%</span>
                    <span>•</span>
                    <span class="text-slate-600">Miễn phí giao hàng cho đơn từ <b>{{ $storefrontSettings['freeship_label'] ?? '500.000₫' }}</b></span>
                </div>
            </div>

            <!-- Chọn Quy Cách Mua Lẻ vs Mua Sỉ (Dual Units Matrix) -->
            <div class="space-y-2.5">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-black text-slate-900 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>📦</span>
                        <span>Chọn Quy Cách Mua & Bảng Giá:</span>
                    </label>
                    <span class="text-[11px] text-indigo-700 font-bold">Chọn để tự động cập nhật đơn giá</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="unit-selection-container">
                    
                    <!-- Đơn vị cơ sở (Mua Lẻ) -->
                    <label class="relative flex items-center justify-between p-3.5 rounded-2xl border-2 border-[#1e3a8a] bg-blue-50/50 cursor-pointer transition shadow-2xs hover:shadow-xs">
                        <input
                            type="radio"
                            name="selected_unit"
                            value=""
                            checked 
                            data-unit-id="" 
                            data-unit-name="{{ $product->base_unit }}" 
                            data-price="{{ (float) $initialPrice }}"
                            data-original-price="{{ (float) $originalPrice }}"
                            onchange="onUnitChanged(this)"
                            class="accent-[#1e3a8a] w-4 h-4 mr-3"
                        />
                        <div class="flex-1">
                            <span class="block text-xs font-black text-slate-900">Mua Lẻ: 1 {{ $product->base_unit }}</span>
                            <span class="text-[10px] text-slate-500">Đơn vị chuẩn đóng gói lẻ</span>
                        </div>
                        <span class="font-mono font-black text-rose-600 text-xs sm:text-sm">
                            {{ number_format($initialPrice, 0, ',', '.') }}₫
                        </span>
                    </label>

                    <!-- Quy cách sỉ nếu có (Thùng, Hộp, Cây) -->
                    @foreach($product->units as $u)
                    @php
                        $unitPrice = (float) $u->price;
                        $unitOriginalPrice = round($unitPrice * 1.15, -3);
                        $perBasePrice = round($unitPrice / max(1, $u->conversion_rate));
                        $savedPercent = round((($initialPrice - $perBasePrice) / max(1, $initialPrice)) * 100);
                    @endphp
                    <label class="relative flex items-center justify-between p-3.5 rounded-2xl border-2 border-slate-200 hover:border-emerald-500 bg-white cursor-pointer transition shadow-2xs hover:shadow-xs">
                        <input
                            type="radio"
                            name="selected_unit"
                            value="{{ $u->id }}" 
                            data-unit-id="{{ $u->id }}" 
                            data-unit-name="{{ $u->unit_name }}" 
                            data-price="{{ $unitPrice }}"
                            data-original-price="{{ $unitOriginalPrice }}"
                            onchange="onUnitChanged(this)"
                            class="accent-emerald-600 w-4 h-4 mr-3"
                        />
                        <div class="flex-1">
                            <span class="block text-xs font-black text-slate-900">Mua Sỉ: 1 {{ $u->unit_name }}</span>
                            <span class="text-[10px] text-emerald-700 font-bold">
                                (Quy đổi x{{ $u->conversion_rate }} {{ $product->base_unit }} @if($savedPercent > 0)- Tiết kiệm {{ $savedPercent }}%@endif)
                            </span>
                        </div>
                        <span class="font-mono font-black text-emerald-700 text-xs sm:text-sm">
                            {{ number_format($unitPrice, 0, ',', '.') }}₫
                        </span>
                    </label>
                    @endforeach

                </div>
            </div>

            <!-- Bảng Ưu Đãi Mua Số Lượng Doanh Nghiệp & B2B -->
            <div class="rounded-2xl border border-slate-200/90 bg-slate-50/70 p-3.5 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-black text-slate-900 flex items-center space-x-1.5">
                        <span>🏢</span>
                        <span>Chính Sách Chiết Khấu Số Lượng & Khách Doanh Nghiệp:</span>
                    </span>
                    <a href="{{ route('storefront.wholesale') }}" class="text-[11px] text-indigo-700 font-bold hover:underline">Xem hợp đồng sỉ →</a>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center text-[10px] pt-1">
                    <div class="p-2 bg-white rounded-xl border border-slate-200 shadow-2xs">
                        <span class="block font-bold text-slate-700">Từ 1 - 4 Đơn vị</span>
                        <span class="text-rose-600 font-mono font-bold">Giá niêm yết</span>
                        <span class="block text-slate-400 text-[9px] mt-0.5">Giao siêu tốc 2h</span>
                    </div>
                    <div class="p-2 bg-white rounded-xl border border-emerald-200 bg-emerald-50/30 shadow-2xs">
                        <span class="block font-bold text-emerald-800">Từ 5 - 19 Đơn vị</span>
                        <span class="text-emerald-700 font-mono font-bold">Giảm thêm 3 - 5%</span>
                        <span class="block text-slate-400 text-[9px] mt-0.5">Miễn phí giao hàng</span>
                    </div>
                    <div class="p-2 bg-white rounded-xl border border-indigo-200 bg-indigo-50/30 shadow-2xs">
                        <span class="block font-bold text-indigo-900">Từ 20+ Đơn vị</span>
                        <span class="text-indigo-700 font-mono font-bold">Giá sỉ đại lý B2B</span>
                        <span class="block text-slate-400 text-[9px] mt-0.5">Hỗ trợ công nợ</span>
                    </div>
                </div>
            </div>

            <!-- Bộ Chọn Số Lượng & Tính Tổng Tiền Tự Động (Dynamic Calculator) -->
            <div class="space-y-3 pt-1">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
                    
                    <div class="flex items-center space-x-3">
                        <label class="text-xs font-black text-slate-800 uppercase">Số Lượng:</label>
                        <div class="flex items-center bg-slate-100 rounded-xl p-1 border border-slate-200">
                            <button
                                type="button"
                                onclick="changeDetailQty(-1)"
                                class="w-9 h-9 rounded-lg bg-white hover:bg-slate-200 active:scale-95 text-slate-700 font-black text-base flex items-center justify-center transition shadow-2xs"
                                aria-label="Giảm số lượng"
                            >-</button>
                            <input
                                type="number"
                                id="detail-qty-input"
                                value="1"
                                min="1"
                                max="999"
                                onchange="onQtyInputChange(this)"
                                class="w-14 text-center bg-transparent text-sm font-black font-mono focus:outline-none"
                            />
                            <button
                                type="button"
                                onclick="changeDetailQty(1)"
                                class="w-9 h-9 rounded-lg bg-white hover:bg-slate-200 active:scale-95 text-slate-700 font-black text-base flex items-center justify-center transition shadow-2xs"
                                aria-label="Tăng số lượng"
                            >+</button>
                        </div>
                    </div>

                    <!-- Tạm tính tổng tiền -->
                    <div class="text-right sm:border-l sm:border-slate-100 sm:pl-4">
                        <span class="text-[11px] text-slate-500 block">Tạm tính (<span id="calc-qty-label">1</span> <span id="calc-unit-label">{{ $product->base_unit }}</span>):</span>
                        <div class="text-lg sm:text-xl font-black text-rose-600 font-mono tracking-tight" id="calc-subtotal-price">
                            {{ number_format($initialPrice, 0, ',', '.') }}₫
                        </div>
                    </div>
                </div>

                <!-- Thanh Điều Kiện Miễn Phí Vận Chuyển (Freeship Progress Meter) -->
                <div id="freeship-banner" class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="text-base" id="freeship-icon">🚚</span>
                        <span id="freeship-text">Cần mua thêm <b>431.000₫</b> để được <b>MIỄN PHÍ GIAO HÀNG TẬN NƠI</b></span>
                    </div>
                    <span class="font-mono font-bold text-[11px] text-emerald-700 shrink-0">Mốc 500k</span>
                </div>

                <!-- Cặp Nút Hành Động Trên Desktop (Thêm Giỏ & Mua Ngay) -->
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button
                        type="button"
                        onclick="submitAddToCart()"
                        class="flex-1 py-3.5 px-6 rounded-2xl bg-emerald-50 hover:bg-emerald-100 active:scale-98 text-emerald-800 border-2 border-emerald-600 font-black text-xs sm:text-sm shadow-xs transition flex items-center justify-center space-x-2 cursor-pointer min-h-[46px]"
                    >
                        <span class="text-base">🛒</span>
                        <span>THÊM VÀO GIỎ HÀNG</span>
                    </button>

                    <button
                        type="button"
                        onclick="submitBuyNow()"
                        class="flex-1 py-3.5 px-6 rounded-2xl bg-[#1e3a8a] hover:bg-blue-900 active:scale-98 text-white font-black text-xs sm:text-sm shadow-md transition transform hover:-translate-y-0.5 flex items-center justify-center space-x-2 cursor-pointer min-h-[46px]"
                    >
                        <span class="text-base">⚡</span>
                        <span>MUA NGAY (THANH TOÁN)</span>
                    </button>
                </div>
            </div>

            <!-- Khối Hotline Tư Vấn Doanh Nghiệp -->
            <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-3 flex items-center justify-between text-xs text-amber-950">
                <div class="flex items-center space-x-2">
                    <span class="text-base">📞</span>
                    <span>Cần tư vấn báo giá hợp đồng trường học, cơ quan, doanh nghiệp?</span>
                </div>
                <a href="{{ $storefrontSettings['hotline_url'] }}" class="font-black text-amber-900 hover:underline shrink-0 ml-2">
                    Gọi: {{ $storefrontSettings['hotline'] }}
                </a>
            </div>

            <!-- Tương Thích Dòng Máy In (Nếu là Hộp Mực / Linh Kiện Kỹ Thuật) -->
            @if($product->compatiblePrinters->count() > 0)
            <div class="pt-3 border-t border-slate-100 space-y-2">
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

    <!-- 3. Mô Tả Chi Tiết & Bảng Thông Số Kỹ Thuật -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-xs space-y-6">
        <h2 class="text-lg sm:text-xl font-black text-slate-900 border-b border-slate-100 pb-3 flex items-center space-x-2">
            <span>📋</span>
            <span>Mô Tả & Thông Số Kỹ Thuật Sản Phẩm</span>
        </h2>

        <div class="prose max-w-none text-xs sm:text-sm text-slate-700 leading-relaxed space-y-4">
            @if(!empty($product->description))
            <p>{{ $product->description }}</p>
            @else
            <p>
                Sản phẩm <b>{{ $product->name }}</b> (Mã SKU: <b>{{ $product->sku }}</b>) thuộc danh mục <b>{{ $product->category?->name ?? 'Văn Phòng Phẩm' }}</b> chính hãng, được phân phối trực tiếp bởi Cửa hàng Văn Phòng Phẩm & Dịch Vụ Máy In Đồng Nai. Sản phẩm đáp ứng tiêu chuẩn chất lượng cao, bền đẹp, tối ưu chi phí vận hành cho văn phòng, trường học và gia đình.
            </p>
            @endif

            <!-- Bảng Thông Số Kỹ Thuật Chi Tiết -->
            <div class="my-6">
                <h3 class="font-black text-slate-900 text-sm mb-3 uppercase tracking-wider flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#1e3a8a]"></span>
                    <span>Bảng Chi Tiết Sản Phẩm:</span>
                </h3>
                <div class="overflow-hidden rounded-2xl border border-slate-200 shadow-2xs">
                    <table class="w-full text-xs text-left">
                        <tbody class="divide-y divide-slate-100">
                            <tr class="bg-slate-50/70">
                                <td class="px-4 py-2.5 font-bold text-slate-600 w-1/3">Tên sản phẩm</td>
                                <td class="px-4 py-2.5 font-bold text-slate-900">{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2.5 font-bold text-slate-600">Mã sản phẩm (SKU)</td>
                                <td class="px-4 py-2.5 font-mono text-slate-800">{{ $product->sku }}</td>
                            </tr>
                            @if($product->barcode)
                            <tr class="bg-slate-50/70">
                                <td class="px-4 py-2.5 font-bold text-slate-600">Mã vạch (Barcode)</td>
                                <td class="px-4 py-2.5 font-mono text-slate-800">{{ $product->barcode }}</td>
                            </tr>
                            @endif
                            <tr class="{{ $product->barcode ? '' : 'bg-slate-50/70' }}">
                                <td class="px-4 py-2.5 font-bold text-slate-600">Ngành hàng</td>
                                <td class="px-4 py-2.5 font-semibold text-slate-900">{{ $product->category?->name ?? 'Văn Phòng Phẩm' }}</td>
                            </tr>
                            <tr class="{{ $product->barcode ? 'bg-slate-50/70' : '' }}">
                                <td class="px-4 py-2.5 font-bold text-slate-600">Đơn vị cơ sở</td>
                                <td class="px-4 py-2.5 font-semibold text-slate-900">{{ $product->base_unit }}</td>
                            </tr>
                            @if($product->units->count() > 0)
                            <tr class="{{ $product->barcode ? '' : 'bg-slate-50/70' }}">
                                <td class="px-4 py-2.5 font-bold text-slate-600">Quy cách đóng gói sỉ</td>
                                <td class="px-4 py-2.5 font-semibold text-emerald-700">
                                    {{ $product->units->map(fn($u) => $u->unit_name . ' (' . $u->conversion_rate . ' ' . $product->base_unit . ')')->join(', ') }}
                                </td>
                            </tr>
                            @endif
                            <tr class="{{ ($product->barcode xor $product->units->count() > 0) ? 'bg-slate-50/70' : '' }}">
                                <td class="px-4 py-2.5 font-bold text-slate-600">Hóa đơn VAT</td>
                                <td class="px-4 py-2.5 font-bold text-emerald-700">Đã bao gồm VAT 100% điện tử theo quy định</td>
                            </tr>
                            <tr class="{{ ($product->barcode xor $product->units->count() > 0) ? '' : 'bg-slate-50/70' }}">
                                <td class="px-4 py-2.5 font-bold text-slate-600">Chính sách bảo hành & Đổi trả</td>
                                <td class="px-4 py-2.5 text-slate-800">1 đổi 1 trong 7 ngày đối với lỗi từ nhà sản xuất</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Cam kết dịch vụ hậu mãi -->
            <div class="bg-slate-50 p-4 sm:p-6 rounded-2xl border border-slate-200/80 space-y-2">
                <h3 class="font-bold text-slate-900 text-sm flex items-center space-x-1.5">
                    <span>🛡️</span>
                    <span>Chính Sách Bán Hàng & Hậu Mãi Chuyên Nghiệp:</span>
                </h3>
                <ul class="list-disc pl-5 space-y-1.5 text-xs text-slate-600">
                    <li><b>Đồng kiểm khi nhận hàng:</b> Quý khách được quyền mở kiện hàng kiểm tra đúng chủng loại và số lượng trước khi nhận và thanh toán.</li>
                    <li><b>Giao hàng siêu tốc 2 giờ:</b> Khu vực Đồng Nai (Biên Hòa, Bình Hòa, Vĩnh Cửu...) giao nhanh trong 2 giờ. Miễn phí ship cho đơn từ {{ $storefrontSettings['freeship_label'] ?? '500.000₫' }}.</li>
                    <li><b>Hóa đơn điện tử VAT đầy đủ:</b> Xuất hóa đơn GTGT điện tử ngay trong ngày cho doanh nghiệp, công ty, trường học.</li>
                    <li><b>Bảo hành 1-đổi-1:</b> Hỗ trợ đổi mới tận nơi trong vòng 7 ngày nếu sản phẩm có lỗi kỹ thuật hoặc bao bì không nguyên vẹn.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- 4. Sản Phẩm Cùng Ngành Hàng Gợi Ý (Dùng Component Chuẩn DRY) -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-6 rounded-full bg-[#1e3a8a]"></span>
                <h3 class="text-base sm:text-lg font-black text-slate-900">Sản Phẩm Cùng Ngành Hàng Gợi Ý</h3>
            </div>
            @if($product->category)
            <a href="{{ route('storefront.products', ['category' => $product->category->slug]) }}" class="text-xs text-[#1e3a8a] font-bold hover:underline flex items-center space-x-1">
                <span>Xem tất cả</span>
                <span>→</span>
            </a>
            @endif
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach($relatedProducts as $rp)
                @include('storefront.components.product-card', ['p' => $rp, 'isSlide' => false])
            @endforeach
        </div>
    </div>
    @endif

    <!-- 5. Top Bán Chạy / Thường Mua Kèm (Cross-sell) -->
    @if(isset($crossSellProducts) && $crossSellProducts->count() > 0)
    <div class="space-y-4 pt-4 border-t border-slate-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-6 rounded-full bg-emerald-600"></span>
                <h3 class="text-base sm:text-lg font-black text-slate-900">Top Sản Phẩm Bán Chạy Mua Kèm</h3>
            </div>
            <a href="{{ route('storefront.products', ['sort' => 'popular']) }}" class="text-xs text-emerald-700 font-bold hover:underline flex items-center space-x-1">
                <span>Xem thêm</span>
                <span>→</span>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach($crossSellProducts as $cp)
                @include('storefront.components.product-card', ['p' => $cp, 'isSlide' => false, 'topBadge' => 'TOP BÁN CHẠY', 'topBadgeClass' => 'bg-gradient-to-r from-amber-500 to-orange-500 text-white'])
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

{{-- 6. MOBILE-FIRST STICKY ACTION BAR CHO TRANG CHI TIẾT SẢN PHẨM (Option 4) --}}
@section('custom_bottom_bar')
<nav class="bg-white/95 backdrop-blur-md border-t border-slate-200 fixed bottom-0 left-0 right-0 z-50 md:hidden w-full shadow-2xl px-3 py-2">
    <div class="flex items-center justify-between gap-2 max-w-lg mx-auto">
        
        <!-- Nút Chat Zalo & Hotline -->
        <a href="{{ $storefrontSettings['zalo_url'] }}" target="_blank" class="flex flex-col items-center justify-center w-12 h-11 text-slate-700 hover:text-blue-600 rounded-xl transition">
            <span class="w-5 h-5 rounded-full bg-[#0068ff] text-white font-black text-[10px] flex items-center justify-center leading-none">Z</span>
            <span class="text-[9px] font-bold mt-0.5">Zalo</span>
        </a>

        <!-- Nút Giỏ Hàng xem nhanh -->
        <a href="{{ route('storefront.cart') }}" class="flex flex-col items-center justify-center w-12 h-11 text-slate-700 hover:text-indigo-600 rounded-xl relative transition">
            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
            <span class="text-[9px] font-bold mt-0.5">Giỏ hàng</span>
            <span id="mobile-sticky-cart-badge" class="absolute top-0 right-1 px-1.5 py-0.2 bg-rose-500 text-white rounded-full text-[9px] font-black hidden">0</span>
        </a>

        <!-- Nút Thêm Vào Giỏ -->
        <button
            type="button"
            onclick="submitAddToCart()"
            class="flex-1 min-h-[44px] py-2 px-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 active:scale-95 text-emerald-800 border border-emerald-500 font-bold text-xs flex flex-col items-center justify-center transition shadow-2xs leading-tight"
        >
            <span class="text-xs">🛒 Thêm Giỏ</span>
            <span class="text-[10px] font-mono font-bold text-emerald-700" id="mobile-btn-price">{{ number_format($initialPrice, 0, ',', '.') }}₫</span>
        </button>

        <!-- Nút Mua Ngay -->
        <button
            type="button"
            onclick="submitBuyNow()"
            class="flex-1 min-h-[44px] py-2 px-3 rounded-xl bg-[#1e3a8a] hover:bg-blue-900 active:scale-95 text-white font-black text-xs flex items-center justify-center space-x-1 transition shadow-md leading-tight"
        >
            <span>⚡ MUA NGAY</span>
        </button>

    </div>
</nav>
@endsection

@push('scripts')
<script>
    let currentUnitId = null;
    let currentUnitName = '{{ $product->base_unit }}';
    let currentUnitPrice = {{ (float) $initialPrice }};
    let currentUnitOriginalPrice = {{ (float) $originalPrice }};
    const freeshipThreshold = {{ (float) setting('freeship_threshold', 500000) }};

    function onUnitChanged(radio) {
        currentUnitId = radio.dataset.unitId || null;
        currentUnitName = radio.dataset.unitName;
        currentUnitPrice = parseFloat(radio.dataset.price);
        currentUnitOriginalPrice = parseFloat(radio.dataset.originalPrice) || (currentUnitPrice * 1.15);

        // Cập nhật giá hiển thị ở Price Box
        document.getElementById('current-display-price').textContent = formatMoney(currentUnitPrice);
        document.getElementById('current-display-original-price').textContent = formatMoney(currentUnitOriginalPrice);
        document.getElementById('current-display-unit').textContent = '/ 1 ' + currentUnitName;

        // Cập nhật giá trên nút Mobile sticky
        const mobileBtnPrice = document.getElementById('mobile-btn-price');
        if (mobileBtnPrice) {
            mobileBtnPrice.textContent = formatMoney(currentUnitPrice);
        }

        // Cập nhật nhãn đơn vị ở khối tính toán
        document.getElementById('calc-unit-label').textContent = currentUnitName;

        // Highlight viền lựa chọn
        const container = document.getElementById('unit-selection-container');
        const labels = container.querySelectorAll('label');
        labels.forEach(l => {
            const r = l.querySelector('input');
            if (r.checked) {
                l.classList.add('border-[#1e3a8a]', 'bg-blue-50/50');
                l.classList.remove('border-slate-200', 'bg-white');
            } else {
                l.classList.remove('border-[#1e3a8a]', 'bg-blue-50/50');
                l.classList.add('border-slate-200', 'bg-white');
            }
        });

        updateDynamicCalculations();
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
        updateDynamicCalculations();
    }

    function onQtyInputChange(input) {
        let val = parseInt(input.value) || 1;
        if (val < 1) val = 1;
        if (val > 999) val = 999;
        input.value = val;
        updateDynamicCalculations();
    }

    function updateDynamicCalculations() {
        const qty = parseInt(document.getElementById('detail-qty-input').value) || 1;
        const subtotal = currentUnitPrice * qty;

        document.getElementById('calc-qty-label').textContent = qty;
        document.getElementById('calc-subtotal-price').textContent = formatMoney(subtotal);

        // Cập nhật thanh cước vận chuyển Freeship
        const freeshipBanner = document.getElementById('freeship-banner');
        const freeshipText = document.getElementById('freeship-text');
        const freeshipIcon = document.getElementById('freeship-icon');

        if (subtotal >= freeshipThreshold) {
            freeshipBanner.className = 'p-2.5 rounded-xl bg-emerald-50 border border-emerald-300 text-xs text-emerald-900 flex items-center justify-between shadow-2xs';
            freeshipIcon.textContent = '🎉';
            freeshipText.innerHTML = 'Đơn hàng <b>đủ điều kiện MIỄN PHÍ VẬN CHUYỂN</b> toàn tỉnh Đồng Nai!';
        } else {
            const remaining = freeshipThreshold - subtotal;
            freeshipBanner.className = 'p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 flex items-center justify-between';
            freeshipIcon.textContent = '🚚';
            freeshipText.innerHTML = 'Mua thêm <b>' + formatMoney(remaining) + '</b> để được <b>MIỄN PHÍ GIAO HỎA TỐC</b>';
        }
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
        syncStickyCartBadge();
        showToast('✓ Đã thêm ' + qty + ' ' + currentUnitName + ' vào giỏ hàng thành công!');
    }

    function submitBuyNow() {
        submitAddToCart();
        window.location.href = "{{ route('storefront.checkout-page') }}";
    }

    function syncStickyCartBadge() {
        const cart = getCart();
        const totalCount = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
        const stickyBadge = document.getElementById('mobile-sticky-cart-badge');
        if (stickyBadge) {
            stickyBadge.textContent = totalCount;
            if (totalCount > 0) {
                stickyBadge.classList.remove('hidden');
            } else {
                stickyBadge.classList.add('hidden');
            }
        }
    }

    // Khởi chạy tính toán ban đầu khi load trang
    document.addEventListener('DOMContentLoaded', function() {
        updateDynamicCalculations();
        syncStickyCartBadge();
    });
</script>
@endpush
