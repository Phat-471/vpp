@extends('layouts.storefront')

@section('title', 'Tra Cứu Đơn Hàng & Phiếu Sửa Chữa Trực Tuyến | ' . ($storefrontSettings['site_name'] ?? 'VPP & Dịch Vụ Máy In'))
@section('meta_description', 'Tra cứu tiến độ giao hàng và sửa chữa máy in trực tuyến nhanh chóng, bảo mật thông tin khách hàng.')

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
            }
          ]
        }
    </script>
@endsection

@section('content')
    <main class="max-w-4xl mx-auto w-full px-4 sm:px-6 py-8 sm:py-12 space-y-6">

        <div class="text-center mb-6 sm:mb-8">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-indigo-500 to-indigo-700 text-white rounded-3xl flex items-center justify-center mx-auto mb-3.5 shadow-md shadow-indigo-100 border border-indigo-300">
                <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">TRA CỨU TIẾN ĐỘ TRỰC TUYẾN</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-lg mx-auto">
                Xem trạng thái giao nhận đơn hàng văn phòng phẩm hoặc tiến độ kiểm tra, sửa chữa máy in dễ dàng và bảo mật.
            </p>
        </div>

        @if($errors->has('lookup_error'))
            <div class="max-w-xl mx-auto mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-xs sm:text-sm flex items-start space-x-2.5 shadow-xs">
                <span class="text-lg leading-none">⚠️</span>
                <span>{{ $errors->first('lookup_error') }}</span>
            </div>
        @endif

        <!-- Form Card (Responsive Layout) -->
        <div class="max-w-xl mx-auto bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200 space-y-5">
            <form action="{{ route('lookup.search') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">
                            1. Mã tra cứu *
                        </label>
                        <span class="text-[10px] text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded-full">Đơn hàng hoặc Phiếu sửa</span>
                    </div>
                    <input type="text" name="code" value="{{ old('code', old('ticket_code', request('code'))) }}"
                        placeholder="VD: HD260001 hoặc SC260001" required autofocus
                        class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 font-mono text-base sm:text-lg font-bold uppercase placeholder:font-sans placeholder:normal-case placeholder:text-slate-400 bg-slate-50 focus:bg-white transition" />
                    
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span class="inline-flex items-center text-[10px] bg-slate-100 text-slate-600 font-semibold px-2 py-0.5 rounded-lg border border-slate-200">
                            📦 Đơn mua sắm: <b class="ml-1 font-mono text-indigo-700">HDxxxx</b>
                        </span>
                        <span class="inline-flex items-center text-[10px] bg-slate-100 text-slate-600 font-semibold px-2 py-0.5 rounded-lg border border-slate-200">
                            🖨️ Phiếu sửa máy: <b class="ml-1 font-mono text-emerald-700">SCxxxx</b>
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                        2. 4 số cuối số điện thoại *
                    </label>
                    <input type="text" name="phone_last4" maxlength="4" value="{{ old('phone_last4') }}"
                        placeholder="VD: 5678" required
                        class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 font-mono text-xl sm:text-2xl font-black tracking-widest text-center placeholder:font-sans placeholder:tracking-normal placeholder:text-slate-400 bg-slate-50 focus:bg-white transition" />
                    <p class="text-[11px] text-slate-400 mt-1">Bảo vệ quyền riêng tư: Chỉ người đặt hàng hoặc gửi máy mới có thể xem thông tin</p>
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-extrabold py-3.5 px-6 rounded-2xl shadow-sm flex items-center justify-center space-x-2 transition transform hover:-translate-y-0.5 text-sm sm:text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>TRA CỨU TIẾN ĐỘ NGAY</span>
                </button>
            </form>
        </div>

        <!-- Trợ giúp khi quên mã -->
        <div class="max-w-xl mx-auto p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-600 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="text-base">💡</span>
                <span>Quý khách bị mất mã hoặc cần hỗ trợ khẩn cấp?</span>
            </div>
            <a href="tel:{{ $storefrontSettings['hotline'] ?? '' }}" class="font-bold text-indigo-700 hover:text-indigo-800 whitespace-nowrap">
                Gọi Hotline →
            </a>
        </div>

    </main>
@endsection