@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, success, danger, outline, ghost
    'size' => 'md', // sm, md, lg
    'href' => null,
    'icon' => null,
    'iconRight' => null,
    'disabled' => false,
    'ariaLabel' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-bold tracking-tight rounded-xl transition duration-150 ease-in-out select-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 active:scale-[0.98] disabled:opacity-60 disabled:pointer-events-none disabled:active:scale-100 touch-target cursor-pointer';

    $sizeClasses = [
        'sm' => 'text-xs px-3 py-2 gap-1.5 min-h-[38px]',
        'md' => 'text-xs sm:text-sm px-4 py-2.5 gap-2 min-h-[44px]',
        'lg' => 'text-sm sm:text-base px-6 py-3.5 gap-2.5 min-h-[48px]',
    ][$size] ?? 'text-xs sm:text-sm px-4 py-2.5 gap-2 min-h-[44px]';

    $variantClasses = [
        'primary' => 'bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 focus-visible:ring-indigo-600 shadow-xs border border-transparent',
        'secondary' => 'bg-slate-100 text-slate-800 hover:bg-slate-200 active:bg-slate-300 focus-visible:ring-slate-400 border border-slate-200/80',
        'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 active:bg-emerald-800 focus-visible:ring-emerald-600 shadow-xs border border-transparent',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-700 active:bg-rose-800 focus-visible:ring-rose-600 shadow-xs border border-transparent',
        'outline' => 'bg-transparent text-slate-700 hover:bg-slate-50 hover:text-indigo-600 active:bg-slate-100 focus-visible:ring-indigo-600 border border-slate-300',
        'ghost' => 'bg-transparent text-slate-600 hover:bg-slate-100 active:bg-slate-200 focus-visible:ring-indigo-600 border border-transparent',
    ][$variant] ?? 'bg-indigo-600 text-white hover:bg-indigo-700 focus-visible:ring-indigo-600 shadow-xs';

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }} @if($ariaLabel) aria-label="{{ $ariaLabel }}" @endif>
        @if($icon)<span class="shrink-0" aria-hidden="true">{{ $icon }}</span>@endif
        <span>{{ $slot }}</span>
        @if($iconRight)<span class="shrink-0" aria-hidden="true">{{ $iconRight }}</span>@endif
    </a>
@else
    <button type="{{ $type }}" @if($disabled) disabled @endif {{ $attributes->merge(['class' => $classes]) }} @if($ariaLabel) aria-label="{{ $ariaLabel }}" @endif>
        @if($icon)<span class="shrink-0" aria-hidden="true">{{ $icon }}</span>@endif
        <span>{{ $slot }}</span>
        @if($iconRight)<span class="shrink-0" aria-hidden="true">{{ $iconRight }}</span>@endif
    </button>
@endif
