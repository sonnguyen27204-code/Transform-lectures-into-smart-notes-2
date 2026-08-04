@props(['lecture'])

@if($lecture->getAudioStreamUrl())
    <x-ui.card padding="false">
        <div class="p-5 bg-gradient-to-br from-brand-500/10 to-accent-500/5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-brand-500 to-accent-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-white">Audio gốc</p>
                    <p class="text-xs text-ink-400">{{ $lecture->original_filename }}</p>
                </div>
            </div>
            <audio controls class="w-full" src="{{ $lecture->getAudioStreamUrl() }}"></audio>
        </div>
    </x-ui.card>
@endif