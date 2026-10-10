@extends('layouts.storefront')

@section('title', ($currentCategory ? $currentCategory->name . ' Giá Sỉ Tận Gốc | ' : 'Văn Phòng Phẩm & Mực Máy In Giá Sỉ | ') . ($storefrontSettings['site_name'] ?? 'VPP & Dịch Vụ Máy In'))
@section('meta_description', 'Khám phá hơn 1.000 mặt hàng văn phòng phẩm, giấy in Double A, PaperOne, bút Thiên Long, hộp mực máy in Canon 2900 giá sỉ tại Đồng Nai. Giao siêu tốc, xuất hóa đơn VAT.')

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
      "name": "Sản phẩm",
      "item": "{{ route('storefront.products') }}"
    }
    @if($currentCategory)
    ,{
      "@type": "ListItem",
      "position": 3,
      "name": "{{ $currentCategory->name }}",
      "item": "{{ route('storefront.products', ['category' => $currentCategory->slug]) }}"
    }
    @endif
  ]
}
</script>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs text-slate-500 font-medium">
        <a href="{{ route('storefront.index') }}" class="hover:text-indigo-600">Trang chủ</a>
        <span>/</span>
        <span class="text-slate-900 font-bold">Danh sách sản phẩm</span>
        @if($currentCategory)
        <span>/</span>
        <span class="text-indigo-600 font-bold">{{ $currentCategory->name }}</span>
        @endif
    </nav>

    <!-- Header Banner Tiêu Đề (Có hình ảnh nền đẹp mắt) -->
    <div class="relative overflow-hidden text-white rounded-3xl p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-slate-700/60 shadow-xl group">
        <!-- Background Image with Dark Gradient Tint Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/banners/banner5.jpg') }}" alt="Danh Mục Sản Phẩm" class="w-full h-full object-cover object-center transform group-hover:scale-105 transition duration-700 opacity-25" />
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-[#0f172a]/95 to-[#1e293b]/90"></div>
        </div>

        <div class="relative z-10 space-y-1.5 max-w-2xl">
            <span class="px-2.5 py-0.5 bg-amber-400 text-slate-950 rounded-full text-[10px] font-black uppercase tracking-wider">
                KHO HÀNG 1.000+ MẶT HÀNG CHÍNH HÃNG
            </span>
            <h1 class="text-xl sm:text-3xl font-black text-white">
                {{ $currentCategory ? $currentCategory->name : 'Tất Cả Sản Phẩm Văn Phòng Phẩm' }}
            </h1>
            <p class="text-xs text-slate-300">
                Bán lẻ theo Ram/Cây và chiết khấu cực sâu theo Thùng/Hộp nguyên đai nguyên kiện. Xuất hóa đơn VAT điện tử.
            </p>
        </div>

        <div class="relative z-10 flex items-center space-x-2 text-xs bg-white/10 px-4 py-2.5 rounded-2xl border border-white/10 flex-shrink-0 backdrop-blur-xs">
            <span class="text-amber-300 font-black text-base">{{ $products->total() }}</span>
            <span class="text-slate-300">sản phẩm khả dụng</span>
        </div>
    </div>

    <!-- Quick Category Horizontal Chips Bar (1 chạm đổi danh mục nhanh) -->
    <div class="bg-white rounded-2xl p-2.5 sm:p-3 border border-slate-200/90 shadow-2xs">
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar scroll-smooth py-0.5">
            <a href="{{ route('storefront.products', array_merge(request()->except(['category', 'page']))) }}" 
               class="flex-shrink-0 px-3.5 py-2 rounded-xl text-xs font-black transition {{ !request('category') ? 'bg-[#1e3a8a] text-white shadow-xs' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200/80' }}">
                <span>Tất Cả Hàng Hoá</span>
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('storefront.products', array_merge(request()->except(['category', 'page']), ['category' => $cat->slug])) }}" 
               class="flex-shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold transition {{ request('category') == $cat->slug ? 'bg-[#1e3a8a] text-white shadow-xs' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200/80' }}">
                <span>{{ $cat->name }}</span>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Main Grid: Sidebar Filters (3 Cols) + Product List (9 Cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Sidebar Bộ Lọc (lg:col-span-3) -->
        <aside class="lg:col-span-3 bg-white p-5 rounded-3xl border border-slate-200 shadow-xs space-y-6">
            
            <!-- Form Lọc -->
            <form action="{{ route('storefront.products') }}" method="GET" class="space-y-5">
                
                <!-- Tìm kiếm theo từ khóa -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tìm kiếm từ khóa</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tên hàng, mã sản phẩm..." class="w-full text-xs pl-8 pr-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50" />
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                <!-- Danh mục ngành hàng -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ngành Hàng</label>
                    <div class="space-y-1.5 max-h-60 overflow-y-auto no-scrollbar text-xs">
                        <a href="{{ route('storefront.products', array_merge(request()->except(['category', 'page']))) }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-xl transition font-medium {{ !request('category') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                            <span>Tất cả ngành hàng</span>
                        </a>
                        @foreach($categories as $cat)
                        <a href="{{ route('storefront.products', array_merge(request()->except(['category', 'page']), ['category' => $cat->slug])) }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-xl transition font-medium {{ request('category') == $cat->slug ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                            <span class="line-clamp-1">{{ $cat->name }}</span>
                            <span class="text-[10px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded-full">{{ $cat->products_count ?: $cat->products()->count() }}</span>
                        </a>
                        @endforeach
                    </div>
                    @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                </div>

                <!-- Lọc theo dòng máy in tương thích -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Dòng Máy In</label>
                    <select name="printer" onchange="this.form.submit()" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 text-slate-800">
                        <option value="">-- Tất cả máy in --</option>
                        @foreach($printerModels as $pm)
                        <option value="{{ $pm->id }}" {{ request('printer') == $pm->id ? 'selected' : '' }}>
                            {{ $pm->brand }} {{ $pm->model_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sắp xếp theo giá / mới nhất -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Sắp Xếp</label>
                    <select name="sort" onchange="this.form.submit()" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 text-slate-800">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên: A - Z</option>
                    </select>
                </div>

                <div class="pt-2 flex gap-2">
                    <button type="submit" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs transition">
                        Áp Dụng
                    </button>
                    @if(request('q') || request('category') || request('printer') || request('sort'))
                    <a href="{{ route('storefront.products') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center justify-center">
                        Xóa
                    </a>
                    @endif
                </div>

            </form>

            <!-- Banner Hỗ Trợ Đơn Sỉ -->
            <div class="p-4 rounded-2xl bg-gradient-to-br from-amber-500/10 to-orange-500/10 border border-amber-200 text-xs space-y-2">
                <span class="font-black text-amber-900 block">⭐ Cần Báo Giá Đơn Sỉ?</span>
                <p class="text-amber-800 text-[11px] leading-relaxed">
                    Doanh nghiệp, trường học mua số lượng lớn nhận ngay chiết khấu 15-20% và công nợ 30 ngày.
                </p>
                <a href="{{ route('storefront.wholesale') }}" class="inline-block text-[11px] font-black text-amber-700 underline hover:text-amber-900">
                    Đăng ký đại lý sỉ →
                </a>
            </div>

        </aside>

        <!-- Product Grid Container (lg:col-span-9) -->
        <section class="lg:col-span-9 space-y-6">

            <!-- Active Filters Tags -->
            @if(request('q') || request('category') || request('printer'))
            <div class="flex flex-wrap items-center gap-2 bg-white p-3 rounded-2xl border border-slate-200 text-xs">
                <span class="text-slate-500 text-[11px] font-semibold">Đang lọc theo:</span>
                @if(request('q'))
                <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 rounded-lg text-slate-800 font-bold">
                    Từ khóa: "{{ request('q') }}"
                </span>
                @endif
                @if($currentCategory)
                <span class="inline-flex items-center px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg font-bold">
                    Ngành: {{ $currentCategory->name }}
                </span>
                @endif
                @if(request('printer'))
                <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-800 rounded-lg font-bold">
                    Dòng máy in: #{{ request('printer') }}
                </span>
                @endif
                <a href="{{ route('storefront.products') }}" class="text-rose-600 hover:underline font-bold text-[11px] ml-auto">
                    Xóa tất cả bộ lọc ✕
                </a>
            </div>
            @endif

            <!-- Products Grid (2 cols mobile, 3 cols tablet, 4 cols desktop) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($products as $p)
                <div
                    onclick="window.location.href='{{ route('storefront.product-detail', $p->slug) }}'"
                    class="bg-white rounded-2xl border border-slate-200/90 hover:border-emerald-500 hover:shadow-xl p-3 sm:p-4 flex flex-col justify-between shadow-xs transition duration-200 group cursor-pointer"
                >
                    <div>
                        <!-- Product Image Box -->
                        <div class="relative w-full aspect-square bg-slate-50 rounded-xl overflow-hidden mb-3 flex items-center justify-center border border-slate-100">
                            <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="w-full h-full object-contain p-2 group-hover:scale-105 transition transform duration-200" loading="lazy" />

                            @if($p->units->count() > 0)
                            <span class="absolute top-1.5 left-1.5 px-2 py-0.5 bg-amber-400 text-slate-950 font-black text-[9px] rounded-md shadow-2xs">
                                BÁN SỈ THÙNG
                            </span>
                            @endif
                        </div>

                        <!-- Product Category & SKU -->
                        <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1">
                            <span class="truncate max-w-[90px]">{{ $p->category?->name ?? 'Văn Phòng Phẩm' }}</span>
                            <span class="font-mono text-slate-500 font-semibold">{{ $p->sku }}</span>
                        </div>

                        <!-- Product Title -->
                        <h3 class="font-bold text-xs sm:text-sm text-slate-900 line-clamp-2 leading-snug group-hover:text-emerald-700 transition mb-2">
                            {{ $p->name }}
                        </h3>
                    </div>

                    <div class="pt-2 border-t border-slate-100 space-y-2">
                        <!-- Price and Stock -->
                        <div class="flex items-baseline justify-between">
                            <div>
                                <span class="font-black text-sm sm:text-base text-rose-600 font-mono">
                                    {{ number_format($p->retail_price, 0, ',', '.') }}₫
                                </span>
                                <span class="text-[10px] text-slate-500 font-medium">/{{ $p->base_unit }}</span>
                            </div>
                            <span class="text-[10px] {{ $p->stock_quantity <= 5 ? 'text-rose-600 font-black' : 'text-emerald-700 font-semibold' }}">
                                {{ $p->stock_quantity > 0 ? 'Còn: ' . $p->stock_quantity : 'Tạm hết' }}
                            </span>
                        </div>

                        <!-- Dual Unit Packaging Price (if any) -->
                        @if($p->units->count() > 0)
                        @php $unit = $p->units->first(); @endphp
                        <div class="text-[10px] text-amber-800 bg-amber-50/80 px-2 py-1 rounded-lg border border-amber-200/60 flex items-center justify-between">
                            <span>Sỉ {{ $unit->unit_name }} (x{{ $unit->conversion_rate }}):</span>
                            <span class="font-mono font-bold">{{ number_format($unit->price, 0, ',', '.') }}₫</span>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-2 gap-1.5 pt-1">
                            <button
                                type="button"
                                onclick="event.stopPropagation(); addToCart({{ $p->id }}, null, '{{ addslashes($p->name) }}', '{{ $p->base_unit }}', {{ (float) $p->retail_price }}, '{{ $p->image_url }}')"
                                class="w-full py-2 px-2 bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 rounded-xl text-[11px] font-bold transition flex items-center justify-center space-x-1 border border-emerald-200"
                            >
                                <span>+ Thêm</span>
                            </button>
                            <span
                                class="w-full py-2 px-2 bg-slate-100 group-hover:bg-emerald-600 group-hover:text-white text-slate-800 rounded-xl text-[11px] font-bold transition text-center flex items-center justify-center"
                            >
                                Chi Tiết →
                            </span>
                        </div>
                    </div>

                </div>
                @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-slate-200 space-y-3">
                    <span class="text-4xl block">🔍</span>
                    <h3 class="text-base font-bold text-slate-800">Không tìm thấy sản phẩm phù hợp</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        Thử tìm kiếm với từ khóa khác hoặc bấm nút bên dưới để xem toàn bộ danh mục sản phẩm.
                    </p>
                    <a href="{{ route('storefront.products') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow-xs">
                        Xem tất cả sản phẩm
                    </a>
                </div>
                @endforelse
            </div>

            <!-- Pagination Links -->
            <div class="pt-6">
                {{ $products->withQueryString()->links() }}
            </div>

        </section>

    </div>

</div>
@endsection
