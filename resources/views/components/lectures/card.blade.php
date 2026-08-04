@props(['lecture'])

@php
    $status = $lecture->status ?? 'pending';
    $statusClasses = [
        'pending' => 'bg-amber-200 text-amber-900 dark:bg-amber-600 dark:text-amber-200',
        'uploading' => 'bg-amber-200 text-amber-900 dark:bg-amber-600 dark:text-amber-200',
        'transcribing' => 'bg-blue-100 text-blue-900 dark:bg-blue-700 dark:text-blue-300',
        'analyzing' => 'bg-blue-100 text-blue-900 dark:bg-blue-700 dark:text-blue-300',
        'generating' => 'bg-emerald-200 text-emerald-900 dark:bg-emerald-600 dark:text-emerald-200',
        'completed' => 'bg-emerald-200 text-emerald-900 dark:bg-emerald-600 dark:text-emerald-200',
        'failed' => 'bg-rose-200 text-rose-900 dark:bg-rose-600 dark:text-rose-200',
    ][$status] ?? 'bg-neutral-100 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300';

    $statusLabels = [
        'pending' => 'Đang chờ',
        'uploading' => 'Đang tải lên',
        'transcribing' => 'Đang phiên âm',
        'analyzing' => 'Đang phân tích',
        'generating' => 'Đang tạo nội dung',
        'completed' => 'Hoàn thành',
        'failed' => 'Thất bại',
    ][$status] ?? ucfirst($status);

    $hasTranscript = $lecture->transcript !== null;
    $hasSummary = $lecture->summary !== null;
    $quizCount = $lecture->quizzes_count ?? 0;
@endphp

<a
    href="{{ route('lectures.show', $lecture) }}"
    class="group block rounded-xl border border-neutral-200 bg-white p-4 transition hover:border-neutral-300 hover:bg-neutral-50 dark:border-white/5 dark:bg-neutral-900 dark:hover:border-white/10 dark:hover:bg-neutral-800"
>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex shrink-0 items-center gap-1 rounded-md px-1.5 py-0.5 text-xs font-medium {{ $statusClasses }}">
                    {{ $statusLabels }}
                </span>
                @if($quizCount > 0)
                    <span class="inline-flex shrink-0 items-center gap-1 rounded-md bg-neutral-100 px-1.5 py-0.5 text-xs font-medium text-neutral-600 dark:bg-white/5 dark:text-neutral-400">
                        <svg class="size-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ $quizCount }}
                    </span>
                @endif
            </div>
            <h3 class="truncate text-base font-semibold text-neutral-950 group-hover:text-blue-500 dark:text-white dark:group-hover:text-blue-300">
                {{ $lecture->title }}
            </h3>
            @if($lecture->description)
                <p class="mt-1 line-clamp-2 text-sm text-neutral-500 dark:text-neutral-500">
                    {{ $lecture->description }}
                </p>
            @endif
        </div>
    </div>

    <div class="mt-3 flex items-center gap-2 text-xs text-neutral-500 dark:text-neutral-500">
        @if($hasTranscript)
            <span class="inline-flex items-center gap-1">
                <svg class="size-2.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Transcript
            </span>
        @endif
        @if($hasSummary)
            <span class="inline-flex items-center gap-1">
                <svg class="size-2.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Summary
            </span>
        @endif
        <span class="ml-auto">{{ $lecture->created_at->diffForHumans() }}</span>
    </div>
</a>