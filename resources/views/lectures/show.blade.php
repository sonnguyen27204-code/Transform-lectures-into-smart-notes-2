@extends('layouts.app')

@section('title', $lecture->title)

@section('page_actions')
    @if($lecture->isCompleted())
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-ink-300 hover:text-white hover:bg-white/10 text-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            In
        </button>
    @endif

    <form method="POST" action="{{ route('lectures.destroy', $lecture) }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa bài giảng này?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 hover:bg-rose-500/20 text-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Xóa
        </button>
    </form>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <x-lectures.header :lecture="$lecture" />

    {{-- Processing state --}}
    @if($lecture->isProcessing())
        <x-lectures.processing-state :lecture="$lecture" />
    @endif

    {{-- Failed state --}}
    @if($lecture->isFailed())
        <x-ui.alert type="error">
            <strong>Xử lý thất bại:</strong> {{ $lecture->error_message ?? 'Lỗi không xác định.' }}
        </x-ui.alert>
    @endif

    {{-- Completed: full UI --}}
    @if($lecture->isCompleted())
        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-lectures.audio-player :lecture="$lecture" />
                <x-lectures.transcript :lecture="$lecture" />
            </div>

            <div class="space-y-6">
                <x-lectures.summary :lecture="$lecture" />
                <x-lectures.quiz-list :lecture="$lecture" />
                <x-lectures.flashcard-list :lecture="$lecture" />
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function seekAudio(seconds) {
    const audios = document.querySelectorAll('audio');
    audios.forEach(a => {
        if (a.duration) {
            a.currentTime = seconds;
            a.play().catch(() => {});
        }
    });
}

function checkAnswer(btn, quizId, selectedIndex, correctIndex) {
    const container = btn.closest('[id^="quiz-"]');
    const allBtns = container.querySelectorAll('.quiz-option');
    const feedback = document.getElementById('quiz-feedback-' + quizId);

    allBtns.forEach((b, idx) => {
        b.disabled = true;
        b.classList.remove('hover:border-brand-400/50', 'hover:bg-brand-500/10');
        if (idx === correctIndex) {
            b.classList.add('bg-emerald-500/20', 'border-emerald-500/50', 'text-emerald-200');
            b.querySelector('span').classList.add('bg-emerald-500/30', 'border-emerald-400');
        }
        if (idx === selectedIndex && idx !== correctIndex) {
            b.classList.add('bg-rose-500/20', 'border-rose-500/50', 'text-rose-200');
        }
    });

    if (selectedIndex !== correctIndex) {
        feedback.classList.remove('hidden');
        feedback.classList.add('bg-rose-500/10', 'border', 'border-rose-500/30', 'text-rose-300');
        feedback.innerHTML = '❌ Chưa đúng. Đáp án đúng là <strong>' + ['A', 'B', 'C', 'D'][correctIndex] + '</strong>.';
        feedback.classList.add('p-3');
    } else {
        feedback.classList.remove('hidden');
        feedback.classList.add('bg-emerald-500/10', 'border', 'border-emerald-500/30', 'text-emerald-300');
        feedback.innerHTML = '✅ Chính xác! Tuyệt vời.';
        feedback.classList.add('p-3');
    }
}

@if($lecture->isProcessing())
(function poll() {
    fetch('{{ route('lectures.status', $lecture) }}')
        .then(r => r.json())
        .then(data => {
            if (data.is_completed || data.is_failed) {
                window.location.reload();
            }
        })
        .catch(() => {})
        .finally(() => setTimeout(poll, 3000));
})();
@endif
</script>
@endpush
@endsection