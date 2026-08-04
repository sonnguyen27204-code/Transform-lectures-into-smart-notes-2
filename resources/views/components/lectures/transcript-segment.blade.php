@props(['segment'])

<div class="flex gap-3 group hover:bg-white/2 -mx-2 px-2 py-1.5 rounded-lg transition-colors cursor-pointer"
     onclick="seekAudio({{ $segment->start_time }})"
     data-start="{{ $segment->start_time }}">
    <div class="flex-shrink-0 text-right">
        <div class="text-xs font-mono text-brand-400 group-hover:text-brand-300 transition-colors">
            {{ $segment->getFormattedStart() }}
        </div>
        <div class="text-[10px] text-ink-500 mt-0.5">
            @if($segment->speaker === 'teacher')
                <span class="text-brand-400">● GV</span>
            @elseif($segment->speaker === 'student')
                <span class="text-accent-400">● HV</span>
            @else
                <span class="text-slate-500">●</span>
            @endif
        </div>
    </div>
    <div class="flex-1 text-sm text-ink-200 leading-relaxed">{{ $segment->text }}</div>
</div>