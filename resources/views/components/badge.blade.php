@props([
    'variant' => 'slate', // slate, emerald, amber, rose, indigo, sky
    'size' => 'md', // sm, md
    'dot' => false,
])

@php
    $baseClasses = 'inline-flex items-center font-bold tracking-tight select-none';

    $sizeClasses = [
        'sm' => 'text-[10px] px-2 py-0.5 rounded-md gap-1',
        'md' => 'text-xs px-2.5 py-1 rounded-lg gap-1.5',
    ][$size] ?? 'text-xs px-2.5 py-1 rounded-lg gap-1.5';

    $variantClasses = [
        'slate' => 'bg-slate-100 text-slate-700 border border-slate-200/80',
        'emerald' => 'bg-emerald-50 text-emerald-700 border border-emerald-200/80',
        'amber' => 'bg-amber-50 text-amber-700 border border-amber-200/80',
        'rose' => 'bg-rose-50 text-rose-700 border border-rose-200/80',
        'indigo' => 'bg-indigo-50 text-indigo-700 border border-indigo-200/80',
        'sky' => 'bg-sky-50 text-sky-700 border border-sky-200/80',
    ][$variant] ?? 'bg-slate-100 text-slate-700 border border-slate-200/80';

    $dotColors = [
        'slate' => 'bg-slate-500',
        'emerald' => 'bg-emerald-500',
        'amber' => 'bg-amber-500',
        'rose' => 'bg-rose-500',
        'indigo' => 'bg-indigo-500',
        'sky' => 'bg-sky-500',
    ][$variant] ?? 'bg-slate-500';

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColors }}" aria-hidden="true"></span>
    @endif
    {{ $slot }}
</span>
