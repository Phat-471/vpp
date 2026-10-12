@props([
    'icon' => 'box',
    'title' => 'Không tìm thấy dữ liệu',
    'description' => 'Hiện tại chưa có mục nào trong danh mục hoặc tìm kiếm này.',
    'actionText' => null,
    'actionHref' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-3xl border border-slate-200/90 p-8 sm:p-12 text-center shadow-xs space-y-4 max-w-lg mx-auto w-full']) }}>
    <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-center text-3xl mx-auto shadow-xs" aria-hidden="true">
        @if($icon === 'search')
            <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        @elseif($icon === 'box')
            <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7.5L12 3l8 4.5v9L12 21l-8-4.5v-9zM4 7.5l8 4.5m0 9v-9m0 0l8-4.5" /></svg>
        @else
            {{ $icon }}
        @endif
    </div>

    <div class="space-y-1.5">
        <h3 class="text-base sm:text-lg font-bold text-slate-800 text-balance">
            {{ $title }}
        </h3>
        <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto text-pretty">
            {{ $description }}
        </p>
    </div>

    @if($actionText && $actionHref)
        <div class="pt-2">
            <x-button :href="$actionHref" variant="primary" size="md">
                {{ $actionText }}
            </x-button>
        </div>
    @endif

    {{ $slot }}
</div>
