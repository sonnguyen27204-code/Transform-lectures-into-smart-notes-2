@props(['quiz', 'index' => 1])

<div class="bg-ink-900/50 rounded-xl p-4 border border-white/5" id="quiz-{{ $quiz->id }}">
    <div class="flex items-start gap-2 mb-3">
        <span class="flex-shrink-0 w-6 h-6 rounded-md bg-gradient-to-br from-brand-500 to-accent-500 flex items-center justify-center text-xs font-bold text-white">{{ $index }}</span>
        <p class="text-sm font-medium text-white leading-snug flex-1">{{ $quiz->question }}</p>
        <span class="text-xs px-2 py-0.5 rounded-md
            {{ $quiz->difficulty === 'easy' ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20' : '' }}
            {{ $quiz->difficulty === 'medium' ? 'bg-amber-500/10 text-amber-300 border border-amber-500/20' : '' }}
            {{ $quiz->difficulty === 'hard' ? 'bg-rose-500/10 text-rose-300 border border-rose-500/20' : '' }}">
            {{ $quiz->difficulty }}
        </span>
    </div>

    <div class="space-y-2 mb-3">
        @foreach($quiz->options as $opt)
            <x-lectures.quiz-option :quiz="$quiz" :option="$opt" />
        @endforeach
    </div>

    <div class="hidden text-xs rounded-lg p-3 mt-2" id="quiz-feedback-{{ $quiz->id }}"></div>
</div>