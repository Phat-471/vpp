@props([
    'icon' => '📦',
    'title' => 'Không tìm thấy dữ liệu',
    'description' => 'Hiện tại chưa có mục nào trong danh mục hoặc tìm kiếm này.',
    'actionText' => null,
    'actionHref' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-3xl border border-slate-200/90 p-8 sm:p-12 text-center shadow-xs space-y-4 max-w-lg mx-auto w-full']) }}>
    <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-center text-3xl mx-auto shadow-xs" aria-hidden="true">
        {{ $icon }}
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
