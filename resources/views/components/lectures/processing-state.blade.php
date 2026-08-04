@props(['lecture'])

@php
    $latestJob = $lecture->processingJobs()->latest()->first();
    $message = $latestJob->message ?? 'Đang chuyển giọng nói thành văn bản...';
    $progress = $latestJob->progress ?? 20;
@endphp

<div id="processing-card" class="glass rounded-2xl p-8 text-center border-brand-500/30">
    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-500/30 to-accent-500/20 flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-brand-400 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
    </div>
    <h3 class="font-display font-semibold text-white text-xl mb-2">AI đang xử lý bài giảng...</h3>
    <p class="text-ink-400 mb-6" id="processing-message">{{ $message }}</p>
    <div class="max-w-md mx-auto">
        <x-ui.progress :progress="$progress" :show-percent="true" />
    </div>
    <p class="text-xs text-ink-500 mt-4">Trang sẽ tự động tải lại khi hoàn thành.</p>
</div>