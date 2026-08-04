@props(['lecture'])

@if($lecture->summary)
    <x-ui.card title="Tóm tắt" :description="'Sentiment: ' . $lecture->summary->sentiment">
        <p class="text-sm text-ink-200 leading-relaxed mb-4">{{ $lecture->summary->brief }}</p>

        @if($lecture->summary->key_takeaways)
            <div class="mb-4">
                <p class="text-xs uppercase tracking-wider text-ink-500 font-semibold mb-3">Key Takeaways</p>
                <ul class="space-y-2">
                    @foreach($lecture->summary->key_takeaways as $i => $takeaway)
                        <li class="flex gap-3 text-sm text-ink-200">
                            <span class="flex-shrink-0 w-5 h-5 rounded-md bg-gradient-to-br from-brand-500 to-accent-500 flex items-center justify-center text-xs font-bold text-white">{{ $i + 1 }}</span>
                            <span>{{ $takeaway }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($lecture->summary->topics && count($lecture->summary->topics))
            <div class="pt-4 border-t border-white/5">
                <p class="text-xs uppercase tracking-wider text-ink-500 font-semibold mb-2">Chủ đề</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($lecture->summary->topics as $topic)
                        <span class="px-2.5 py-1 rounded-lg bg-accent-500/10 border border-accent-500/20 text-accent-300 text-xs">{{ $topic }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </x-ui.card>
@endif