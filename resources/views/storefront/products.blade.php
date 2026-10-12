@extends('layouts.storefront')

@section('title', ($currentCategory ? $currentCategory->name . ' Giá Sỉ Tận Gốc | ' : 'Văn Phòng Phẩm & Hộp Mực Máy In Giá Sỉ | ') . ($storefrontSettings['site_name'] ?? 'VPP & Dịch Vụ Máy In'))
@section('meta_description', 'Khám phá hơn 1.000 mặt hàng văn phòng phẩm, giấy in photo Double A, PaperOne, bút Thiên Long, hộp mực máy in Canon 2900 giá sỉ theo thùng tại Đồng Nai. Giao siêu tốc 2H, hóa đơn VAT đầy đủ.')

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
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 space-y-6">

    <!-- 1. BREADCRUMB ĐIỀU HƯỚNG -->
    <nav aria-label="Breadcrumb" class="flex items-center space-x-2 text-xs text-slate-500 font-medium">
        <a href="{{ route('storefront.index') }}" class="hover:text-emerald-700 transition">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('storefront.products') }}" class="{{ !$currentCategory ? 'text-slate-900 font-bold' : 'hover:text-emerald-700' }} transition">Danh mục sản phẩm</a>
        @if($currentCategory)
        <span>/</span>
        <span class="text-emerald-700 font-bold">{{ $currentCategory->name }}</span>
        @endif
    </nav>

    <!-- 2. HEADER BANNER TIÊU ĐỀ SANG TRỌNG -->
    <div class="relative overflow-hidden text-white rounded-3xl p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border border-slate-700/60 shadow-xl group">
        <!-- Background Image with Dark Gradient Tint Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/banners/banner5.jpg') }}" alt="Danh Mục Sản Phẩm Văn Phòng Phẩm" 
                 width="800" height="300" loading="lazy" decoding="async"
                 class="w-full h-full object-cover object-center transform group-hover:scale-105 transition duration-700 opacity-30" />
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-[#0b173d]/95 to-[#060c20]/90"></div>
            <!-- Glow accents -->
            <div class="absolute -top-16 -right-16 w-60 h-60 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        <div class="relative z-10 space-y-2 max-w-2xl">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-2.5 py-0.5 bg-amber-400 text-slate-950 rounded-full text-[10px] font-black uppercase tracking-wider">
                    Danh mục sản phẩm
                </span>
                <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full text-[10px] font-bold">
                    Giao hàng nhanh
                </span>
                <span class="px-2.5 py-0.5 bg-white/10 text-slate-200 border border-white/10 rounded-full text-[10px] font-bold">
                    Có hóa đơn điện tử
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white leading-tight">
                {{ $currentCategory ? $currentCategory->name : 'Tổng Kho Văn Phòng Phẩm & Thiết Bị Máy In' }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                {{ $currentCategory ? ($currentCategory->description ?: 'Các sản phẩm chất lượng cao, giá sỉ tận gốc cho cơ quan, doanh nghiệp và trường học tại Đồng Nai.') : 'Bán lẻ theo Ram/Cây và chiết khấu cực sâu theo Thùng/Hộp nguyên đai nguyên kiện. Hỗ trợ công nợ 30 ngày.' }}
            </p>
        </div>

        <div class="relative z-10 flex items-center space-x-3 text-xs bg-white/10 px-5 py-3 rounded-2xl border border-white/15 shrink-0 backdrop-blur-md shadow-sm">
            <div>
                <span class="text-amber-300 font-mono font-black text-lg block leading-none">{{ number_format($products->total()) }}</span>
                <span class="text-slate-300 text-[11px]">sản phẩm khả dụng</span>
            </div>
        </div>
    </div>

    <!-- 4. THANH ĐIỀU KHIỂN & CÔNG CỤ BỘ LỌC (SEARCH + SORT + VIEW MODE + NÚT MỞ DRAWER MOBILE) -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            
            <!-- Left: Tìm kiếm nhanh & Bộ lọc nhanh Mobile -->
            <div class="flex items-center gap-2 w-full sm:w-auto flex-1 max-w-lg">

                <!-- Nút Mở Bộ Lọc Mobile (Drawer Toggle) -->
                <button type="button" onclick="toggleMobileFilter(true)" 
                        class="lg:hidden shrink-0 px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-200 flex items-center space-x-1.5 transition">
                    <span>Bộ lọc</span>
                    @php
                        $activeFiltersCount = (request('category') ? 1 : 0) + (request('brand') ? 1 : 0) + (request('printer') ? 1 : 0) + (request('price_range') || request('min_price') || request('max_price') ? 1 : 0) + (request('in_stock') ? 1 : 0) + (request('flash_sale') ? 1 : 0) + (request('best_seller') ? 1 : 0);
                    @endphp
                    @if($activeFiltersCount > 0)
                    <span class="w-4 h-4 rounded-full bg-rose-600 text-white text-[9px] font-black flex items-center justify-center">{{ $activeFiltersCount }}</span>
                    @endif
                </button>
            </div>

            <!-- Right: Sắp xếp & Chế độ xem Lưới / Danh sách -->
            <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0">
                
                <!-- Dropdown Sắp Xếp -->
                <div class="flex items-center space-x-2 text-xs">
                    <span class="text-slate-500 font-semibold hidden md:inline">Sắp xếp:</span>
                    <form action="{{ route('storefront.products') }}" method="GET" id="sortForm">
                        @foreach(request()->except(['sort', 'page']) as $k => $v)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        <select name="sort" onchange="document.getElementById('sortForm').submit()" 
                                class="text-xs py-2 px-3 rounded-xl border border-slate-300 bg-slate-50 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]">
                            <option value="popular" {{ request('sort', 'popular') == 'popular' ? 'selected' : '' }}>Phổ biến</option>
                            <option value="discount" {{ request('sort') == 'discount' ? 'selected' : '' }}>Giảm giá nhiều nhất</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: thấp đến cao</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: cao đến thấp</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên: A đến Z</option>
                        </select>
                    </form>
                </div>

                <!-- Chế độ xem: Grid vs List Switcher -->
                <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <a href="{{ route('storefront.products', array_merge(request()->query(), ['view' => 'grid'])) }}" 
                       class="p-1.5 rounded-lg text-xs font-bold transition flex items-center justify-center {{ request('view', 'grid') == 'grid' ? 'bg-white text-[#1e3a8a] shadow-xs' : 'text-slate-500 hover:text-slate-800' }}"
                       title="Chế độ xem dạng Lưới (Grid)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </a>
                    <a href="{{ route('storefront.products', array_merge(request()->query(), ['view' => 'list'])) }}" 
                       class="p-1.5 rounded-lg text-xs font-bold transition flex items-center justify-center {{ request('view') == 'list' ? 'bg-white text-[#1e3a8a] shadow-xs' : 'text-slate-500 hover:text-slate-800' }}"
                       title="Chế độ xem dạng Danh Sách (List)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </a>
                </div>

            </div>

        </div>

    </div>

    <!-- 5. MAIN CONTENT LAYOUT: SIDEBAR BỘ LỌC ĐA CHIỀU (3 COLS) + DANH SÁCH SẢN PHẨM (9 COLS) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Sidebar Bộ Lọc Desktop (lg:col-span-3) -->
        <aside class="hidden lg:block lg:col-span-3 bg-white p-5 rounded-3xl border border-slate-200/90 shadow-2xs space-y-6 sticky top-20">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Bộ lọc</h3>
                </div>
                @if($activeFiltersCount > 0 || request('q'))
                <a href="{{ route('storefront.products') }}" class="text-[11px] font-bold text-rose-600 hover:underline">
                    Xóa tất cả ✕
                </a>
                @endif
            </div>

            <!-- Form Lọc Đa Chiều Desktop -->
            <form action="{{ route('storefront.products') }}" method="GET" class="space-y-6">
                @if(request('q'))
                <input type="hidden" name="q" value="{{ request('q') }}">
                @endif
                @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('view'))
                <input type="hidden" name="view" value="{{ request('view') }}">
                @endif

                <!-- Nhóm 1: Ngành hàng sản phẩm -->
                <div class="space-y-2">
                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">Danh mục</h4>
                    <div class="space-y-1 max-h-52 overflow-y-auto no-scrollbar text-xs">
                        <label class="flex items-center space-x-2 py-1 px-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="category" value="" onchange="this.form.submit()" 
                                   {{ !request('category') ? 'checked' : '' }} class="text-[#1e3a8a] focus:ring-[#1e3a8a]">
                            <span class="text-slate-700 font-medium">Tất cả ngành hàng</span>
                        </label>
                        @foreach($categories as $cat)
                        <label class="flex items-center justify-between py-1 px-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <input type="radio" name="category" value="{{ $cat->slug }}" onchange="this.form.submit()" 
                                       {{ request('category') == $cat->slug ? 'checked' : '' }} class="text-[#1e3a8a] focus:ring-[#1e3a8a]">
                                <span class="text-slate-700 font-medium line-clamp-1">{{ $cat->name }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono">({{ $cat->products_count }})</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Nhóm 2: Khoảng giá phổ biến -->
                <div class="space-y-2 border-t border-slate-100 pt-4">
                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">Khoảng Giá (VNĐ)</h4>
                    <div class="space-y-1.5 text-xs">
                        <label class="flex items-center space-x-2 py-1 px-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="price_range" value="" onchange="this.form.submit()" 
                                   {{ !request('price_range') ? 'checked' : '' }} class="text-[#1e3a8a] focus:ring-[#1e3a8a]">
                            <span class="text-slate-700">Tất cả mức giá</span>
                        </label>
                        <label class="flex items-center space-x-2 py-1 px-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="price_range" value="under_50k" onchange="this.form.submit()" 
                                   {{ request('price_range') == 'under_50k' ? 'checked' : '' }} class="text-[#1e3a8a] focus:ring-[#1e3a8a]">
                            <span class="text-slate-700">Dưới 50.000₫</span>
                        </label>
                        <label class="flex items-center space-x-2 py-1 px-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="price_range" value="50k_150k" onchange="this.form.submit()" 
                                   {{ request('price_range') == '50k_150k' ? 'checked' : '' }} class="text-[#1e3a8a] focus:ring-[#1e3a8a]">
                            <span class="text-slate-700">50.000₫ - 150.000₫</span>
                        </label>
                        <label class="flex items-center space-x-2 py-1 px-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="price_range" value="150k_500k" onchange="this.form.submit()" 
                                   {{ request('price_range') == '150k_500k' ? 'checked' : '' }} class="text-[#1e3a8a] focus:ring-[#1e3a8a]">
                            <span class="text-slate-700">150.000₫ - 500.000₫</span>
                        </label>
                        <label class="flex items-center space-x-2 py-1 px-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="price_range" value="over_500k" onchange="this.form.submit()" 
                                   {{ request('price_range') == 'over_500k' ? 'checked' : '' }} class="text-[#1e3a8a] focus:ring-[#1e3a8a]">
                            <span class="text-slate-700">Trên 500.000₫</span>
                        </label>
                    </div>

                    <!-- Nhập khoảng giá tùy chỉnh -->
                    <div class="pt-2 grid grid-cols-2 gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Từ ₫" 
                               class="w-full text-[11px] p-2 rounded-lg border border-slate-300 bg-slate-50" />
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Đến ₫" 
                               class="w-full text-[11px] p-2 rounded-lg border border-slate-300 bg-slate-50" />
                    </div>
                </div>

                <!-- Nhóm 3: Thương hiệu máy in tương thích -->
                <div class="space-y-2 border-t border-slate-100 pt-4">
                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">Hãng Máy In Tương Thích</h4>
                    
                    <!-- Chips Hãng Máy In -->
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        @foreach($printerBrands as $brand)
                        <label class="cursor-pointer">
                            <input type="radio" name="brand" value="{{ $brand }}" onchange="this.form.submit()" class="sr-only" {{ request('brand') == $brand ? 'checked' : '' }}>
                            <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold border transition {{ request('brand') == $brand ? 'bg-[#1e3a8a] text-white border-[#1e3a8a]' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                                {{ $brand }}
                            </span>
                        </label>
                        @endforeach
                    </div>

                    <!-- Dropdown Dòng Máy In Cụ Thể -->
                    <div class="pt-2">
                        <select name="printer" onchange="this.form.submit()" class="w-full text-xs py-2 px-2.5 rounded-xl border border-slate-300 bg-slate-50 text-slate-800 font-medium">
                            <option value="">-- Chi tiết dòng máy --</option>
                            @foreach($printerModels as $pm)
                            <option value="{{ $pm->id }}" {{ request('printer') == $pm->id ? 'selected' : '' }}>
                                {{ $pm->brand }} {{ $pm->model_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Nhóm 4: Tình trạng & Ưu đãi đặc biệt -->
                <div class="space-y-2 border-t border-slate-100 pt-4 text-xs">
                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">Tình Trạng & Ưu Đãi</h4>
                    <div class="space-y-1.5">
                        <label class="flex items-center space-x-2 py-1 px-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="checkbox" name="flash_sale" value="1" onchange="this.form.submit()" 
                                   {{ request('flash_sale') ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-500">
                            <span class="font-bold text-rose-600">Đang ưu đãi</span>
                        </label>
                        <label class="flex items-center space-x-2 py-1 px-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="checkbox" name="best_seller" value="1" onchange="this.form.submit()" 
                                   {{ request('best_seller') ? 'checked' : '' }} class="rounded text-amber-600 focus:ring-amber-500">
                            <span class="font-bold text-amber-800">Bán chạy</span>
                        </label>
                        <label class="flex items-center space-x-2 py-1 px-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="checkbox" name="in_stock" value="1" onchange="this.form.submit()" 
                                   {{ request('in_stock') ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                            <span class="font-bold text-emerald-700">● Chỉ sản phẩm còn hàng</span>
                        </label>
                    </div>
                </div>

                <!-- Nút Áp Dụng Bộ Lọc -->
                <div class="pt-2 flex gap-2">
                    <button type="submit" class="w-full py-2.5 bg-[#1e3a8a] hover:bg-blue-900 text-white rounded-xl text-xs font-black shadow-xs transition">
                        Áp Dụng Lọc
                    </button>
                    @if($activeFiltersCount > 0 || request('q'))
                    <a href="{{ route('storefront.products') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center justify-center">
                        Xóa
                    </a>
                    @endif
                </div>

            </form>

            <!-- Card Báo Giá Sỉ Cho Doanh Nghiệp & Trường Học -->
            <div class="p-4 rounded-2xl bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent border border-amber-300 text-xs space-y-2">
                <div class="flex items-center space-x-1.5 text-amber-900 font-black">
                    <span>Báo giá sỉ</span>
                </div>
                <p class="text-amber-800 text-[11px] leading-relaxed">
                    Nhận báo giá theo số lượng cho doanh nghiệp và trường học.
                </p>
                <a href="{{ route('storefront.wholesale') }}" class="inline-block text-[11px] font-black text-amber-900 underline hover:text-amber-700">
                    Nhận báo giá ngay →
                </a>
            </div>

        </aside>

        <!-- Danh Sách Sản Phẩm (lg:col-span-9) -->
        <section class="lg:col-span-9 space-y-5">

            <!-- Thẻ Badges Bộ Lọc Đang Kích Hoạt (Active Filters) -->
            @if($activeFiltersCount > 0 || request('q'))
            <div class="flex flex-wrap items-center gap-2 bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs text-xs">
                <span class="text-slate-500 text-[11px] font-bold">Đang lọc theo:</span>
                
                @if(request('q'))
                <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 rounded-lg text-slate-800 font-bold space-x-1">
                    <span>Từ khóa: "{{ request('q') }}"</span>
                    <a href="{{ route('storefront.products', request()->except(['q', 'page'])) }}" class="text-slate-400 hover:text-rose-600">✕</a>
                </span>
                @endif

                @if($currentCategory)
                <span class="inline-flex items-center px-2.5 py-1 bg-blue-50 text-blue-800 rounded-lg font-bold space-x-1">
                    <span>Ngành: {{ $currentCategory->name }}</span>
                    <a href="{{ route('storefront.products', request()->except(['category', 'page'])) }}" class="text-blue-400 hover:text-rose-600">✕</a>
                </span>
                @endif

                @if(request('brand'))
                <span class="inline-flex items-center px-2.5 py-1 bg-indigo-50 text-indigo-800 rounded-lg font-bold space-x-1">
                    <span>Hãng máy in: {{ request('brand') }}</span>
                    <a href="{{ route('storefront.products', request()->except(['brand', 'page'])) }}" class="text-indigo-400 hover:text-rose-600">✕</a>
                </span>
                @endif

                @if(request('printer'))
                @php
                    $pmModel = $printerModels->firstWhere('id', request('printer'));
                @endphp
                <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-800 rounded-lg font-bold space-x-1">
                    <span>Dòng máy: {{ $pmModel ? $pmModel->brand . ' ' . $pmModel->model_name : '#' . request('printer') }}</span>
                    <a href="{{ route('storefront.products', request()->except(['printer', 'page'])) }}" class="text-amber-400 hover:text-rose-600">✕</a>
                </span>
                @endif

                @if(request('price_range'))
                <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-800 rounded-lg font-bold space-x-1">
                    <span>Khoảng giá: {{ match(request('price_range')) {
                        'under_50k' => '< 50.000₫',
                        '50k_150k' => '50k - 150k',
                        '150k_500k' => '150k - 500k',
                        'over_500k' => '> 500.000₫',
                        default => request('price_range')
                    } }}</span>
                    <a href="{{ route('storefront.products', request()->except(['price_range', 'page'])) }}" class="text-emerald-400 hover:text-rose-600">✕</a>
                </span>
                @endif

                @if(request('flash_sale'))
                <span class="inline-flex items-center px-2.5 py-1 bg-rose-50 text-rose-700 rounded-lg font-bold space-x-1">
                    <span>Ưu đãi</span>
                    <a href="{{ route('storefront.products', request()->except(['flash_sale', 'page'])) }}" class="text-rose-400 hover:text-rose-600">✕</a>
                </span>
                @endif

                @if(request('best_seller'))
                <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-800 rounded-lg font-bold space-x-1">
                    <span>Bán chạy</span>
                    <a href="{{ route('storefront.products', request()->except(['best_seller', 'page'])) }}" class="text-amber-400 hover:text-rose-600">✕</a>
                </span>
                @endif

                @if(request('in_stock'))
                <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg font-bold space-x-1">
                    <span>● Còn hàng</span>
                    <a href="{{ route('storefront.products', request()->except(['in_stock', 'page'])) }}" class="text-emerald-400 hover:text-rose-600">✕</a>
                </span>
                @endif

                <a href="{{ route('storefront.products') }}" class="text-rose-600 hover:underline font-bold text-[11px] ml-auto shrink-0">
                    Xóa tất cả bộ lọc ✕
                </a>
            </div>
            @endif

            <!-- Hiển thị sản phẩm: CHẾ ĐỘ XEM GRID HOẶC LIST -->
            @if(request('view') == 'list')
                <!-- 1. CHẾ ĐỘ XEM DANH SÁCH (LIST VIEW) -->
                <div class="space-y-4">
                    @forelse($products as $p)
                        @php
                            $hasCustomSalePrice = ($p->is_flash_sale && $p->flash_sale_price && $p->flash_sale_price > 0 && $p->flash_sale_price < $p->retail_price);
                            $displayPrice = $hasCustomSalePrice ? (float)$p->flash_sale_price : (float)$p->retail_price;
                            $originalPrice = (float)$p->retail_price;
                            $discountPercent = $hasCustomSalePrice ? round((($originalPrice - $displayPrice) / $originalPrice) * 100) : 15;
                        @endphp
                        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-emerald-500 transition duration-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 group">
                            
                            <!-- Ảnh sản phẩm & Badges -->
                            <div class="flex items-center space-x-4 w-full sm:w-auto">
                                <a href="{{ route('storefront.product-detail', $p->slug) }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl bg-slate-50 flex items-center justify-center p-2 shrink-0 overflow-hidden border border-slate-100 group-hover:bg-emerald-50/20 transition">
                                    <img src="{{ $p->image_url }}" alt="{{ $p->name }}" width="112" height="112" loading="lazy" decoding="async" class="max-h-full max-w-full object-contain group-hover:scale-105 transition duration-300" />
                                </a>
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5 text-[10px]">
                                        <span class="bg-gradient-to-r from-rose-500 to-orange-500 text-white font-black px-1.5 py-0.5 rounded">-{{ $discountPercent }}%</span>
                                        @if($p->category)
                                        <span class="text-slate-500 font-sans truncate">{{ $p->category->name }}</span>
                                        @endif
                                        <span class="text-slate-400 font-mono">• Mã: {{ $p->sku }}</span>
                                    </div>
                                    <h3 class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-emerald-700 transition leading-snug">
                                        <a href="{{ route('storefront.product-detail', $p->slug) }}">{{ $p->name }}</a>
                                    </h3>
                                    
                                    <!-- Máy in tương thích -->
                                    @if($p->compatiblePrinters && $p->compatiblePrinters->count() > 0)
                                    <div class="flex items-center gap-1 text-[10px] text-slate-500 flex-wrap">
                                        <span class="font-bold text-slate-700">Tương thích:</span>
                                        @foreach($p->compatiblePrinters->take(3) as $cp)
                                        <span class="bg-blue-50 text-blue-800 px-1.5 py-0.5 rounded">{{ $cp->brand }} {{ $cp->model_name }}</span>
                                        @endforeach
                                        @if($p->compatiblePrinters->count() > 3)
                                        <span class="text-slate-400">+{{ $p->compatiblePrinters->count() - 3 }}</span>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Quy cách đóng gói -->
                                    @if($p->units && $p->units->count() > 0)
                                    <div class="flex items-center gap-2 text-[10px] text-slate-600 pt-0.5">
                                        @foreach($p->units->take(2) as $u)
                                        <span class="bg-slate-100 px-2 py-0.5 rounded">
                                            {{ $u->unit_name }}: <strong class="font-mono text-slate-900">{{ number_format($u->price, 0, ',', '.') }}₫</strong>
                                        </span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Cột Giá & Nút Thao Tác -->
                            <div class="w-full sm:w-auto flex sm:flex-col items-center sm:items-end justify-between sm:justify-center border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100 shrink-0 gap-2">
                                <div class="text-left sm:text-right">
                                    <span class="text-base sm:text-lg font-black text-rose-600 font-mono block leading-none">
                                        {{ number_format($displayPrice, 0, ',', '.') }}₫
                                    </span>
                                    <span class="text-[11px] text-slate-400 line-through font-mono">
                                        {{ number_format($originalPrice, 0, ',', '.') }}₫
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('storefront.product-detail', $p->slug) }}" 
                                       class="px-4 py-2 bg-[#1e3a8a] hover:bg-blue-900 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center space-x-1">
                                        <span>Chi Tiết</span>
                                        <span>→</span>
                                    </a>
                                </div>
                            </div>

                        </div>
                    @empty
                        <x-empty-state
                            icon="search"
                            title="Chưa tìm thấy sản phẩm"
                            description="Thử đổi từ khóa hoặc bộ lọc."
                            actionText="Xem tất cả"
                            :actionHref="route('storefront.products')"
                        />
                    @endforelse
                </div>
            @else
                <!-- 2. CHẾ ĐỘ XEM DẠNG LƯỚI (GRID VIEW - MẶC ĐỊNH) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3.5 sm:gap-4">
                    @forelse($products as $p)
                        @include('storefront.components.product-card', ['p' => $p])
                    @empty
                        <div class="col-span-full">
                            <x-empty-state
                                icon="search"
                                title="Chưa tìm thấy sản phẩm"
                                description="Thử đổi từ khóa hoặc bộ lọc."
                                actionText="Xem tất cả"
                                :actionHref="route('storefront.products')"
                            />
                        </div>
                    @endforelse
                </div>
            @endif

            <!-- Phân Trang (Pagination Links) -->
            <div class="pt-6">
                {{ $products->withQueryString()->links() }}
            </div>

        </section>

    </div>

</div>

<!-- 6. DRAWER BỘ LỌC DÀNH CHO MOBILE (OFF-CANVAS FILTER DRAWER) -->
<div id="mobileFilterDrawer" class="fixed inset-0 z-50 hidden" aria-labelledby="filterDrawerTitle" role="dialog" aria-modal="true">
    <!-- Backdrop overlay -->
    <div onclick="toggleMobileFilter(false)" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
    
    <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <div class="w-screen max-w-xs sm:max-w-sm bg-white shadow-2xl flex flex-col justify-between overflow-y-auto">
            
            <!-- Drawer Header -->
            <div class="p-4 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <div class="flex items-center space-x-2">
                    <h3 id="filterDrawerTitle" class="text-sm font-black text-slate-900 uppercase tracking-wider">Bộ lọc</h3>
                </div>
                <button type="button" onclick="toggleMobileFilter(false)" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 text-lg font-bold">
                    ✕
                </button>
            </div>

            <!-- Drawer Body -->
            <form action="{{ route('storefront.products') }}" method="GET" class="p-4 space-y-5 flex-1">
                @if(request('q'))
                <input type="hidden" name="q" value="{{ request('q') }}">
                @endif
                @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('view'))
                <input type="hidden" name="view" value="{{ request('view') }}">
                @endif

                <!-- Ngành hàng -->
                <div>
                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider mb-2">Danh mục</h4>
                    <select name="category" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-300 bg-slate-50 text-slate-800 font-bold">
                        <option value="">-- Tất cả ngành hàng --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->products_count }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Khoảng giá -->
                <div>
                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider mb-2">Khoảng Giá</h4>
                    <select name="price_range" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-300 bg-slate-50 text-slate-800 font-bold">
                        <option value="">-- Tất cả mức giá --</option>
                        <option value="under_50k" {{ request('price_range') == 'under_50k' ? 'selected' : '' }}>Dưới 50.000₫</option>
                        <option value="50k_150k" {{ request('price_range') == '50k_150k' ? 'selected' : '' }}>50.000₫ - 150.000₫</option>
                        <option value="150k_500k" {{ request('price_range') == '150k_500k' ? 'selected' : '' }}>150.000₫ - 500.000₫</option>
                        <option value="over_500k" {{ request('price_range') == 'over_500k' ? 'selected' : '' }}>Trên 500.000₫</option>
                    </select>
                </div>

                <!-- Hãng máy in -->
                <div>
                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider mb-2">Hãng Máy In</h4>
                    <select name="brand" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-300 bg-slate-50 text-slate-800 font-bold mb-2">
                        <option value="">-- Tất cả hãng máy in --</option>
                        @foreach($printerBrands as $brand)
                        <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                        @endforeach
                    </select>

                    <select name="printer" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-300 bg-slate-50 text-slate-800">
                        <option value="">-- Chi tiết dòng máy --</option>
                        @foreach($printerModels as $pm)
                        <option value="{{ $pm->id }}" {{ request('printer') == $pm->id ? 'selected' : '' }}>
                            {{ $pm->brand }} {{ $pm->model_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Trạng thái -->
                <div class="space-y-2 pt-2 border-t border-slate-100 text-xs">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="flash_sale" value="1" {{ request('flash_sale') ? 'checked' : '' }} class="rounded text-rose-600">
                        <span class="font-bold text-rose-600">Đang ưu đãi</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="best_seller" value="1" {{ request('best_seller') ? 'checked' : '' }} class="rounded text-amber-600">
                        <span class="font-bold text-amber-800">Bán chạy</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} class="rounded text-emerald-600">
                        <span class="font-bold text-emerald-700">● Chỉ sản phẩm còn hàng</span>
                    </label>
                </div>

                <!-- Drawer Action Buttons -->
                <div class="pt-4 flex gap-2">
                    <button type="submit" class="flex-1 py-3 bg-[#1e3a8a] text-white rounded-xl text-xs font-black shadow transition">
                        Áp Dụng Lọc
                    </button>
                    <a href="{{ route('storefront.products') }}" class="px-4 py-3 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold transition flex items-center justify-center">
                        Xóa
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleMobileFilter(show) {
        const drawer = document.getElementById('mobileFilterDrawer');
        if (drawer) {
            if (show) {
                drawer.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                drawer.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }
    }
</script>
@endpush
@endsection
