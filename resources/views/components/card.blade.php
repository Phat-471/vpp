@props([
    'as' => 'div',
    'padding' => 'p-5 sm:p-6',
    'hoverable' => false,
])

@php
    $baseClasses = 'bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-xs transition duration-200';
    $hoverClasses = $hoverable ? 'hover:shadow-md hover:border-slate-300' : '';
    $classes = "{$baseClasses} {$hoverClasses} {$padding}";
@endphp

<{{ $as }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $as }}>
