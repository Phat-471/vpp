@extends('layouts.storefront')

@section('title', ($page->meta_title ?: $page->title) . ' | ' . ($storefrontSettings['site_name'] ?? 'VPP & Thiết Bị Máy In'))
@section('meta_description', $page->meta_description ?: $page->summary)

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
      "name": "{{ $page->title }}",
      "item": "{{ url()->current() }}"
    }
  ]
}
</script>
@endsection

@section('content')
<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-slate-500 mb-6">
            <a href="{{ route('storefront.index') }}" class="hover:text-emerald-700 transition flex items-center gap-1 font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Trang chủ
            </a>
            <span class="mx-2 text-slate-400">/</span>
            <span class="text-slate-800 font-bold truncate">{{ $page->title }}</span>
        </nav>

        <!-- Article Card -->
        <article class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Article Header -->
            <div class="p-6 sm:p-10 border-b border-slate-100 bg-gradient-to-br from-blue-50/50 via-white to-slate-50">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>{{ $storefrontSettings['site_name'] ?? 'VPP & Thiết Bị Máy In' }}</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                    {{ $page->title }}
                </h1>

                @if($page->summary)
                    <p class="mt-4 text-base sm:text-lg text-slate-600 leading-relaxed font-normal">
                        {{ $page->summary }}
                    </p>
                @endif

                <div class="mt-6 flex items-center gap-4 text-xs text-slate-400 border-t border-slate-200/60 pt-4">
                    <span>Cập nhật: {{ $page->updated_at->format('d/m/Y') }}</span>
                    <span>•</span>
                    <span>Hỗ trợ: {{ $storefrontSettings['hotline'] ?? '0974.194.305' }}</span>
                </div>
            </div>

            <!-- Article Body -->
            <div class="p-6 sm:p-10 text-slate-700 leading-relaxed space-y-6 prose prose-indigo max-w-none">
                {!! $page->content !!}
            </div>

            <!-- Footer Contact Box -->
            <div class="m-6 sm:m-10 p-6 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Cần hỗ trợ thêm về thông tin hoặc đặt hàng số lượng lớn?</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Hotline hỗ trợ kỹ thuật và kinh doanh phục vụ từ 7h30 đến 18h30 hàng ngày.</p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ $storefrontSettings['hotline_url'] ?? 'tel:0974194305' }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        Gọi {{ $storefrontSettings['hotline'] ?? '0974.194.305' }}
                    </a>
                    <a href="{{ $storefrontSettings['zalo_url'] ?? 'https://zalo.me/0974194305' }}" target="_blank" class="px-4 py-2.5 bg-[#0068ff] hover:bg-blue-600 text-white text-xs font-bold rounded-xl shadow-sm transition">
                        Chat Zalo
                    </a>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection
