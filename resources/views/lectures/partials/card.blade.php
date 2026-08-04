@props(['lecture'])

<a href="{{ route('lectures.show', $lecture) }}" class="group block bg-white dark:bg-stone-900 rounded-2xl border border-stone-200 dark:border-stone-800 overflow-hidden hover:shadow-soft-lg transition-all duration-200">
    {{-- Header --}}
    <div class="relative h-28 bg-gradient-to-br from-stone-100 to-stone-50 dark:from-stone-800 dark:to-stone-900 overflow-hidden">
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-14 h-14 rounded-2xl bg-white dark:bg-stone-700 shadow-soft flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-7 h-7 text-stone-600 dark:text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                </svg>
            </div>
        </div>

        <div class="absolute top-3 right-3">
            <x-ui.status-badge :status="$lecture->status" />
        </div>

        @if($lecture->isCompleted() && $lecture->duration)
            <div class="absolute bottom-3 left-3 px-2.5 py-1 bg-white/90 dark:bg-stone-700/90 rounded-lg text-xs font-medium text-stone-600 dark:text-stone-300">
                {{ $lecture->getDurationFormatted() }}
            </div>
        @endif
    </div>

    {{-- Body --}}
    <div class="p-5">
        <h3 class="font-medium text-stone-900 dark:text-stone-100 text-sm leading-snug mb-2 group-hover:text-stone-600 dark:group-hover:text-stone-300 transition-colors line-clamp-2">
            {{ $lecture->title }}
        </h3>

        @if($lecture->description)
            <p class="text-xs text-stone-500 line-clamp-2 mb-3">{{ $lecture->description }}</p>
        @endif

        <div class="flex items-center justify-between text-xs text-stone-400">
            <div class="flex items-center gap-3">
                @if($lecture->transcript)
                    <span>{{ number_format($lecture->transcript->word_count) }} words</span>
                @endif
                @if($lecture->quizzes_count > 0)
                    <span>{{ $lecture->quizzes_count }} quiz</span>
                @endif
            </div>
            <span>{{ $lecture->created_at->diffForHumans() }}</span>
        </div>
    </div>
</a>
