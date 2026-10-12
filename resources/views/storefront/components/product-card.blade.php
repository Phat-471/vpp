@php
    if (empty($p) || !is_object($p)) {
        \Illuminate\Support\Facades\Log::warning('product-card called with invalid product: ' . (is_scalar($p) ? $p : gettype($p)));
        return;
    }
    $hasCustomSalePrice = ($p->is_flash_sale && $p->flash_sale_price && $p->flash_sale_price > 0 && $p->flash_sale_price < $p->retail_price);
    if ($hasCustomSalePrice) {
        $displayPrice = (float) $p->flash_sale_price;
        $originalPrice = (float) $p->retail_price;
        $discountPercent = round((($originalPrice - $displayPrice) / $originalPrice) * 100);
    } else {
        $displayPrice = (float) $p->retail_price;
    }
    $isSlide = $isSlide ?? false;
@endphp

<a
    href="{{ route('storefront.product-detail', $p->slug) }}"
    class="bg-white rounded-2xl shadow-xs border border-slate-200/90 flex flex-col justify-between hover:shadow-xl hover:border-emerald-500 transition duration-200 group relative overflow-hidden focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 text-inherit no-underline {{ $isSlide ? 'flex-shrink-0 w-[230px] sm:w-[260px] snap-start' : 'w-full' }}"
    aria-label="{{ $p->name }} - Giá {{ number_format($displayPrice, 0, ',', '.') }} đồng"
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
            @if($hasCustomSalePrice)
            <span class="bg-rose-600 text-white font-bold px-2 py-0.5 rounded-md tabular-nums">Giảm {{ $discountPercent }}%</span>
            @endif
            @if($p->stock_quantity > 0)
            <span class="bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded-md border border-emerald-200">● Còn hàng</span>
            @else
            <span class="bg-slate-100 text-slate-500 font-bold px-2 py-0.5 rounded-md border border-slate-200">Tạm hết</span>
            @endif
        </div>

        <!-- Product Thumbnail Packshot (Explicit dimensions prevent CLS) -->
        <div class="block w-full h-36 sm:h-40 bg-slate-50/70 rounded-xl mb-3 flex items-center justify-center overflow-hidden p-2 group-hover:bg-emerald-50/30 transition">
            <img
                src="{{ $p->image_url }}"
                alt="{{ $p->name }}"
                width="240"
                height="240"
                class="max-h-full max-w-full object-contain group-hover:scale-110 transition transform duration-300"
                loading="lazy"
                decoding="async"
            />
        </div>

        <!-- Mã SKU & Danh mục -->
        <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono mb-1">
            <span class="tabular-nums">Mã: {{ $p->sku }}</span>
            @if($p->category)
            <span class="text-slate-500 font-sans truncate max-w-[110px]">{{ $p->category->name }}</span>
            @endif
        </div>

        <!-- Tên sản phẩm -->
        <h3 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug line-clamp-2 mb-2 group-hover:text-emerald-700 transition h-9 sm:h-10 text-pretty">
            {{ $p->name }}
        </h3>

        <!-- Giá sản phẩm -->
        <div class="space-y-1 mb-2.5">
            <div class="flex items-baseline space-x-2 flex-wrap">
                <span class="text-sm sm:text-base font-black {{ $hasCustomSalePrice ? 'text-rose-600' : 'text-slate-900' }} font-mono tabular-nums">
                    {{ number_format($displayPrice, 0, ',', '.') }}&nbsp;₫
                </span>
                @if($hasCustomSalePrice)
                <span class="text-[11px] text-slate-400 line-through font-mono tabular-nums">
                    {{ number_format($originalPrice, 0, ',', '.') }}&nbsp;₫
                </span>
                @endif
            </div>

            <!-- Quy cách đóng gói / Đơn vị tính -->
            @if($p->units && $p->units->count() > 0)
            <div class="pt-0.5 space-y-0.5">
                @foreach($p->units->take(2) as $u)
                <div class="bg-slate-50 text-slate-600 px-2 py-0.5 rounded text-[10px] flex justify-between tabular-nums">
                    <span>{{ $u->unit_name }}:</span>
                    <span class="font-bold text-slate-900 font-mono">{{ number_format($u->price, 0, ',', '.') }}&nbsp;₫</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>

    <!-- Nút hành động -->
    <div class="px-3.5 pb-3.5 pt-2 border-t border-slate-100 grid grid-cols-2 gap-2 mt-auto">
        <span class="w-full py-2 bg-slate-100 group-hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center justify-center text-center">
            Chi Tiết
        </span>
        <button
            type="button"
            onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $p->id }}, null, '{{ addslashes($p->name) }}', '{{ $p->base_unit }}', {{ (float) $displayPrice }}, '{{ $p->image_url }}')"
            aria-label="Thêm {{ $p->name }} vào giỏ hàng"
            class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs rounded-xl transition flex items-center justify-center space-x-1 shadow-xs cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
        >
            <x-heroicon-o-shopping-bag class="w-4 h-4" aria-hidden="true" />
            <span>Mua</span>
        </button>
    </div>
</a>
