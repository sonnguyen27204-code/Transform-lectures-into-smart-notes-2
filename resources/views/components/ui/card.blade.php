@props([
    'title' => null,
    'description' => null,
    'padding' => true,
])

@php
    $paddingClass = $padding === 'false' || $padding === false ? '' : 'p-6';
@endphp

<section
    {{ $attributes->merge(['class' => 'bg-white dark:bg-stone-900 rounded-2xl border border-stone-200 dark:border-stone-800']) }}
>
    @if($title || $description || isset($actions))
        <header class="px-6 py-4 border-b border-stone-200 dark:border-stone-800">
            <div class="flex items-start justify-between gap-3">
                <div>
                    @if($title)
                        <h2 class="text-base font-semibold text-stone-900 dark:text-stone-100">{{ $title }}</h2>
                    @endif
                    @if($description)
                        <p class="mt-1 text-sm text-stone-500">{{ $description }}</p>
                    @endif
                </div>
                @isset($actions)
                    <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
                @endisset
            </div>
        </header>
    @endif

    <div class="{{ $paddingClass }}">
        {{ $slot }}
    </div>
</section>
