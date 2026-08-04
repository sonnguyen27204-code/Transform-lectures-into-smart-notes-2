@props([
    'name',
    'label' => null,
    'value' => null,
    'checked' => false,
])

<label class="flex items-center gap-3 cursor-pointer group">
    <input
        type="radio"
        name="{{ $name }}"
        value="{{ $value ?? $name }}"
        @checked($checked)
        {{ $attributes->merge(['class' => 'w-4 h-4 text-brand-500 bg-ink-900 border-white/20 focus:ring-brand-400 focus:ring-offset-ink-950 cursor-pointer']) }}
    />
    @if($label)
        <span class="text-sm text-ink-200 group-hover:text-white transition-colors">{{ $label }}</span>
    @else
        <span class="text-sm text-ink-200 group-hover:text-white transition-colors">{{ $slot }}</span>
    @endif
</label>