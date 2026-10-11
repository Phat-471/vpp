@props([
    'id' => null,
    'name' => null,
    'label' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'autocomplete' => null,
    'inputmode' => null,
    'spellcheck' => null,
    'hint' => null,
    'error' => null,
])

@php
    $inputId = $id ?? ($name ? 'inp-' . $name : 'inp-' . uniqid());
    $errorId = $inputId . '-error';
@endphp

<div class="space-y-1.5 w-full">
    @if($label)
        <label for="{{ $inputId }}" class="block text-xs font-bold text-slate-700 select-none">
            {{ $label }}
            @if($required)
                <span class="text-rose-500 font-black ml-0.5" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-xl shadow-xs">
        <input
            id="{{ $inputId }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if($inputmode) inputmode="{{ $inputmode }}" @endif
            @if($spellcheck !== null) spellcheck="{{ $spellcheck ? 'true' : 'false' }}" @endif
            @if($error) aria-invalid="true" aria-describedby="{{ $errorId }}" @endif
            {{ $attributes->merge([
                'class' => 'w-full text-xs sm:text-sm px-3.5 py-2.5 sm:py-3 rounded-xl border bg-slate-50/70 focus:bg-white text-slate-900 placeholder:text-slate-400 font-medium transition duration-150 ease-in-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-600 focus-visible:border-transparent min-h-[44px] ' .
                ($error ? 'border-rose-400 focus-visible:ring-rose-500 bg-rose-50/20' : 'border-slate-300 hover:border-slate-400')
            ]) }}
        />
    </div>

    @if($hint && !$error)
        <p class="text-[11px] text-slate-500">{{ $hint }}</p>
    @endif

    @if($error)
        <p id="{{ $errorId }}" class="text-[11px] font-semibold text-rose-600 flex items-center gap-1" aria-live="polite">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ $error }}</span>
        </p>
    @endif
</div>
