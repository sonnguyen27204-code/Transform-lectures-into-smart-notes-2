@props([
    'progress' => 0,
    'showPercent' => false,
    'label' => null,
])

@php
    $progress = max(0, min(100, (int) $progress));
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if($label || $showPercent)
        <div class="mb-1 flex items-center justify-between text-xs text-neutral-600 dark:text-neutral-400">
            @if($label)<span>{{ $label }}</span>@endif
            @if($showPercent)<span>{{ $progress }}%</span>@endif
        </div>
    @endif
    <div class="h-2 w-full overflow-hidden rounded-full bg-neutral-200 dark:bg-white/5">
        <div
            class="h-full rounded-full bg-blue-600 transition-all duration-300"
            style="width: {{ $progress }}%"
        ></div>
    </div>
</div>