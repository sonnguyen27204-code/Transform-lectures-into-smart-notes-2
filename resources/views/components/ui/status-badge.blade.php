@props(['status'])

@php
    $status = $status ?? 'pending';
    $classes = [
        'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'uploading' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'transcribing' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        'analyzing' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        'generating' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
        'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
        'failed' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    ][$status] ?? 'bg-stone-100 text-stone-600 dark:bg-stone-800 dark:text-stone-400';

    $labels = [
        'pending' => 'Pending',
        'uploading' => 'Uploading',
        'transcribing' => 'Transcribing',
        'analyzing' => 'Analyzing',
        'generating' => 'Generating',
        'completed' => 'Completed',
        'failed' => 'Failed',
    ][$status] ?? ucfirst($status);
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $classes]) }}>
    @if(in_array($status, ['pending', 'uploading', 'transcribing', 'analyzing', 'generating']))
        <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5 animate-pulse"></span>
    @endif
    {{ $labels }}
</span>
