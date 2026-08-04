@props(['color' => 'slate', 'label'])

@php
    $classes = match($color) {
        'brand' => 'bg-brand-500/10 border-brand-500/30 text-brand-300',
        'accent' => 'bg-accent-500/10 border-accent-500/30 text-accent-300',
        default => 'bg-slate-500/10 border-slate-500/30 text-slate-300',
    };
    $dotColor = match($color) {
        'brand' => 'bg-brand-400',
        'accent' => 'bg-accent-400',
        default => 'bg-slate-400',
    };
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border {{ $classes }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
    {{ $label }}
</span>