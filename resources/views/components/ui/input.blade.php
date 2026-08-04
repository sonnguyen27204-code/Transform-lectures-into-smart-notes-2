@props([
    'name',
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'required' => false,
    'hint' => null,
    'value' => null,
])

@php
    $isInvalid = $errors->has($name);
    $baseInputClasses = 'block w-full rounded-xl border bg-white dark:bg-stone-900 px-4 py-2.5 text-sm text-stone-900 dark:text-stone-100 placeholder-stone-400 transition-colors focus:outline-none focus:ring-2 focus:ring-stone-900/10 dark:focus:ring-white/10';
    $stateClasses = $isInvalid
        ? 'border-red-500 focus:border-red-500 focus:ring-red-500/10'
        : 'border-stone-200 dark:border-stone-700 focus:border-stone-400';
@endphp

<div {{ $attributes->only('class') }}>
    @if($label || $slot->isNotEmpty())
        <label for="{{ $name }}" class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-1.5">
            {{ $label ?? $slot }}
            @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    @if($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->except(['class'])->merge(['class' => $baseInputClasses . ' ' . $stateClasses]) }}
            rows="4"
        >{{ old($name, $value) }}</textarea>
    @elseif($type === 'select')
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->except(['class'])->merge(['class' => $baseInputClasses . ' ' . $stateClasses . ' cursor-pointer']) }}
        >
            {{ $slot }}
        </select>
    @else
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            placeholder="{{ $placeholder }}"
            value="{{ old($name, $value) }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->except(['class'])->merge(['class' => $baseInputClasses . ' ' . $stateClasses]) }}
        />
    @endif

    @if($hint && !$isInvalid)
        <p class="mt-1.5 text-xs text-stone-500">{{ $hint }}</p>
    @endif

    @if($isInvalid)
        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $errors->first($name) }}</p>
    @endif
</div>
