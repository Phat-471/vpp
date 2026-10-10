@php
    $hasCustomSalePrice = ($p->is_flash_sale && $p->flash_sale_price && $p->flash_sale_price > 0 && $p->flash_sale_price < $p->retail_price);
    if ($hasCustomSalePrice) {
        $displayPrice = (float) $p->flash_sale_price;
        $originalPrice = (float) $p->retail_price;
        $discountPercent = round((($originalPrice - $displayPrice) / $originalPrice) * 100);
    } else {
        $displayPrice = (float) $p->retail_price;
        $discountPercent = ($p->id * 13 + 7) % 20 + 10; // 10-29%
        $originalPrice = $displayPrice * (1 + $discountPercent / 100);
    }
    $soldCount = ($p->id * 37 + 123) % 900 + 100;
    $soldPercent = min(95, ($p->id * 17 + 41) % 60 + 35);
    $isSlide = $isSlide ?? false;
@endphp

<div
    onclick="window.location.href='{{ route('storefront.product-detail', $p->slug) }}'"
    class="bg-white rounded-2xl shadow-xs border border-slate-200/90 flex flex-col justify-between hover:shadow-xl hover:border-emerald-500 transition duration-200 group relative cursor-pointer overflow-hidden {{ $isSlide ? 'flex-shrink-0 w-[230px] sm:w-[260px] snap-start' : 'w-full' }}"
>
    {{-- Huy hiệu góc trên bên trái --}}
    @if(!empty($topBadge))
    <div class="absolute top-0 left-0 z-20">
        <div class="{{ $topBadgeClass ?? 'bg-gradient-to-r from-rose-600 to-orange-500 text-white' }} text-[9px] font-black px-3 py-1 rounded-br-xl uppercase tracking-wider shadow-md">
            {{ $topBadge }}
        </div>
    </div>
    @endif

    <div class="p-3.5 sm:p-4">
        <!-- Badges Header -->
        <div class="flex items-center justify-between gap-1 text-[10px] mb-2">
            <span class="bg-gradient-to-r from-rose-500 to-orange-500 text-white font-black px-2 py-0.5 rounded-md shadow-xs">
                -{{ $discountPercent }}%
            </span>
            @if($p->stock_quantity > 0)
            <span class="bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded-md border border-emerald-200">● Còn hàng</span>
            @else
            <span class="bg-slate-100 text-slate-500 font-bold px-2 py-0.5 rounded-md border border-slate-200">Tạm hết</span>
            @endif
        </div>

        <!-- Product Thumbnail Packshot -->
        <div class="block w-full h-36 sm:h-40 bg-slate-50/70 rounded-xl mb-3 flex items-center justify-center overflow-hidden p-2 group-hover:bg-emerald-50/30 transition">
            <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="max-h-full max-w-full object-contain group-hover:scale-110 transition transform duration-300" loading="lazy" />
        </div>

        <!-- Mã SKU & Danh mục -->
        <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono mb-1">
            <span>Mã: {{ $p->sku }}</span>
            @if($p->category)
            <span class="text-slate-500 font-sans truncate max-w-[100px]">{{ $p->category->name }}</span>
            @endif
        </div>

        <!-- Tên sản phẩm -->
        <h3 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug line-clamp-2 mb-2 group-hover:text-emerald-700 transition h-9 sm:h-10">
            {{ $p->name }}
        </h3>

        <!-- Đánh giá sao + Đã bán -->
        <div class="flex items-center justify-between text-[11px] mb-2">
            <div class="flex items-center space-x-1 text-amber-400">
                <span>★★★★★</span>
                <span class="text-[10px] text-slate-400 font-semibold">(5.0)</span>
            </div>
            <span class="text-[10px] text-slate-400 font-semibold">Đã bán {{ number_format($soldCount) }}</span>
        </div>

        <!-- Giá sản phẩm -->
        <div class="space-y-1 mb-2.5">
            <div class="flex items-baseline space-x-2 flex-wrap">
                <span class="text-sm sm:text-base font-black text-rose-600 font-mono">
                    {{ number_format($displayPrice, 0, ',', '.') }}₫
                </span>
                <span class="text-[11px] text-slate-400 line-through font-mono">
                    {{ number_format($originalPrice, 0, ',', '.') }}₫
                </span>
            </div>

            <!-- Quy cách đóng gói / Đơn vị tính -->
            @if($p->units && $p->units->count() > 0)
            <div class="pt-0.5 space-y-0.5">
                @foreach($p->units->take(2) as $u)
                <div class="bg-slate-50 text-slate-600 px-2 py-0.5 rounded text-[10px] flex justify-between">
                    <span>{{ $u->unit_name }}:</span>
                    <span class="font-bold text-slate-900 font-mono">{{ number_format($u->price, 0, ',', '.') }}₫</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        @if(!empty($showProgress))
        <!-- Thanh tiến trình "Đã bán" Flash Sale -->
        <div class="mb-1">
            <div class="w-full h-3.5 bg-rose-100 rounded-full overflow-hidden relative">
                <div class="h-full bg-gradient-to-r from-rose-500 to-orange-400 rounded-full transition-all duration-500 flex items-center justify-end pr-1" style="width: {{ $soldPercent }}%">
                    <span class="text-[8px] text-white font-black whitespace-nowrap">{{ $soldPercent }}%</span>
                </div>
            </div>
            <div class="flex justify-between text-[9px] text-slate-400 mt-0.5 px-0.5">
                <span>🔥 Đang bán chạy</span>
                <span class="text-rose-500 font-bold">Còn {{ 100 - $soldPercent }}%</span>
            </div>
        </div>
        @endif
    </div>

    <!-- Nút hành động -->
    <div class="px-3.5 pb-3.5 pt-2 border-t border-slate-100 grid grid-cols-2 gap-2 mt-auto">
        <span class="w-full py-2 bg-slate-100 group-hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center justify-center text-center">
            Chi Tiết
        </span>
        <button
            type="button"
            onclick="event.stopPropagation(); addToCart({{ $p->id }}, null, '{{ addslashes($p->name) }}', '{{ $p->base_unit }}', {{ (float) $displayPrice }}, '{{ $p->image_url }}')"
            class="w-full py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 active:from-emerald-800 active:to-emerald-800 text-white font-bold text-xs rounded-xl transition flex items-center justify-center space-x-1 shadow-xs"
        >
            <span>🛒 Mua</span>
        </button>
    </div>
</div>
