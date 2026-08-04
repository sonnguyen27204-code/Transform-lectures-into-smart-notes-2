@props(['lecture'])

<div class="flex items-start gap-3">
    <a href="{{ route('lectures.index') }}" class="p-2 rounded-lg text-ink-400 hover:text-white hover:bg-white/5 transition-colors mt-1">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-2 mb-2">
            <x-ui.status-badge :status="$lecture->status" />
            <span class="text-xs text-ink-500">{{ $lecture->created_at->format('d/m/Y H:i') }}</span>
            @if($lecture->isCompleted() && $lecture->duration)
                <span class="text-xs text-ink-500">·</span>
                <span class="text-xs text-ink-500 font-mono">{{ $lecture->getDurationFormatted() }}</span>
            @endif
        </div>
        <h1 class="font-display text-3xl font-bold text-white leading-tight">{{ $lecture->title }}</h1>
        @if($lecture->description)
            <p class="text-ink-400 mt-2">{{ $lecture->description }}</p>
        @endif
    </div>
</div>