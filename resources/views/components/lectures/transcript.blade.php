@props(['lecture'])

@if($lecture->transcript)
    @php
        $speakers = $lecture->transcript->segments->pluck('speaker')->unique();
    @endphp

    <x-ui.card
        title="Transcript"
        :description="number_format($lecture->transcript->word_count) . ' từ · ' . $lecture->transcript->segments->count() . ' đoạn'"
    >
        {{-- Speaker legend --}}
        <div class="flex flex-wrap gap-2 mb-4 pb-4 border-b border-white/5">
            @foreach($speakers as $sp)
                @if($sp === 'teacher')
                    <x-lectures.speaker-badge color="brand" label="Giảng viên" />
                @elseif($sp === 'student')
                    <x-lectures.speaker-badge color="accent" label="Học viên" />
                @else
                    <x-lectures.speaker-badge color="slate" label="Khác" />
                @endif
            @endforeach
        </div>

        {{-- Segments --}}
        <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2" id="transcript-list">
            @foreach($lecture->transcript->segments as $seg)
                <x-lectures.transcript-segment :segment="$seg" />
            @endforeach
        </div>
    </x-ui.card>
@endif