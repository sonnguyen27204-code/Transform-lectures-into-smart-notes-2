@props(['quiz', 'option'])

<button type="button"
        onclick="checkAnswer(this, {{ $quiz->id }}, {{ $option->index }}, {{ $quiz->correct_index }})"
        class="quiz-option w-full text-left px-3 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm text-ink-200 hover:border-brand-400/50 hover:bg-brand-500/10 transition-all flex items-center gap-2">
    <span class="flex-shrink-0 w-6 h-6 rounded-md bg-ink-800 border border-white/10 flex items-center justify-center text-xs font-bold">{{ chr(65 + $option->index) }}</span>
    <span>{{ $option->text }}</span>
</button>