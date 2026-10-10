@extends('layouts.storefront')

@section('title', 'Tra Cứu Tiến Độ Sửa Máy In & Nạp Mực Online | ' . ($storefrontSettings['site_name'] ?? 'VPP & Dịch Vụ Máy In'))
@section('meta_description', 'Tra cứu tiến độ sửa chữa máy in, nạp mực trực tuyến nhanh chóng, bảo mật thông tin khách hàng.')

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
              "name": "Tra cứu phiếu sửa chữa",
              "item": "{{ route('lookup.index') }}"
            }
          ]
        }
        </script>
@endsection

@section('content')
    <main class="max-w-4xl mx-auto w-full px-4 sm:px-6 py-8 sm:py-12 space-y-6">

        <div class="text-center mb-8">
            <div
                class="w-16 h-16 sm:w-20 sm:h-20 bg-indigo-100 text-indigo-700 rounded-3xl flex items-center justify-center mx-auto mb-3 shadow-sm border border-indigo-200">
                <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">TRA CỨU TIẾN ĐỘ</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto">
                Nhập Mã phiếu và 4 số cuối số điện thoại gửi máy để xem tình trạng sửa chữa.
            </p>
        </div>

        @if($errors->has('lookup_error'))
            <div
                class="max-w-xl mx-auto mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-xs sm:text-sm flex items-start space-x-2.5 shadow-sm">
                <span class="text-lg leading-none">⚠️</span>
                <span>{{ $errors->first('lookup_error') }}</span>
            </div>
        @endif

        <!-- Form Card (Responsive Layout) -->
        <div class="max-w-xl mx-auto bg-white p-6 sm:p-8 rounded-3xl shadow-lg border border-slate-200">
            <form action="{{ route('lookup.search') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                        1. Mã phiếu tiếp nhận *
                    </label>
                    <input type="text" name="ticket_code" value="{{ old('ticket_code', request('code')) }}"
                        placeholder="VD: SC260001" required
                        class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 font-mono text-base sm:text-lg font-bold uppercase placeholder:font-sans placeholder:normal-case placeholder:text-slate-400 bg-slate-50" />
                    <p class="text-[11px] text-slate-400 mt-1">In trên phiếu hẹn hoặc tem dán trên thân máy</p>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                        2. 4 số cuối số điện thoại gửi máy *
                    </label>
                    <input type="text" name="phone_last4" maxlength="4" value="{{ old('phone_last4') }}"
                        placeholder="VD: 5678" required
                        class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 font-mono text-xl sm:text-2xl font-black tracking-widest text-center placeholder:font-sans placeholder:tracking-normal placeholder:text-slate-400 bg-slate-50" />
                    <p class="text-[11px] text-slate-400 mt-1">Để bảo mật thông tin, chỉ người gửi máy mới có thể tra cứu</p>
                </div>

                <button type="submit"
                    class="w-full bg-[#1e3a8a] hover:bg-blue-900 active:bg-blue-950 text-white font-extrabold py-3.5 px-6 rounded-2xl shadow-md flex items-center justify-center space-x-2 transition transform hover:-translate-y-0.5 text-sm sm:text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>TRA CỨU TIẾN ĐỘ</span>
                </button>
            </form>
        </div>

    </main>
@endsection