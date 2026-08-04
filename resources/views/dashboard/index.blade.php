@extends('layouts.app')

@section('title', 'Dashboard')

@section('page_actions')
    <a href="{{ route('lectures.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-stone-900 dark:bg-stone-100 text-white dark:text-stone-900 rounded-xl text-sm font-medium hover:bg-stone-800 dark:hover:bg-stone-200 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Upload
    </a>
@endsection

@section('content')
<div class="space-y-10">

    {{-- Welcome --}}
    <div class="pt-4">
        <p class="text-sm text-stone-500 mb-1">{{ now()->format('l, M j') }}</p>
        <h1 class="text-3xl font-semibold text-stone-900 dark:text-stone-50">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }}
        </h1>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $statsCards = [
                ['label' => 'Total Lectures', 'value' => $stats['total'], 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'color' => 'stone'],
                ['label' => 'Completed', 'value' => $stats['completed'], 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'emerald'],
                ['label' => 'Processing', 'value' => $stats['processing'], 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'color' => 'amber'],
                ['label' => 'Duration', 'value' => gmdate('H:i', $stats['total_duration'] ?? 0), 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'blue'],
            ];
        @endphp

        @foreach($statsCards as $s)
            <div class="bg-white dark:bg-stone-900 rounded-2xl p-5 border border-stone-200 dark:border-stone-800">
                <div class="w-10 h-10 rounded-xl bg-stone-100 dark:bg-stone-800 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-stone-600 dark:text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $s['icon'] }}"/>
                    </svg>
                </div>
                <div class="text-2xl font-semibold text-stone-900 dark:text-stone-50 mb-1">{{ $s['value'] }}</div>
                <div class="text-sm text-stone-500">{{ $s['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Recent Lectures --}}
    <div>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-stone-900 dark:text-stone-50">Recent Lectures</h2>
            <a href="{{ route('lectures.index') }}" class="text-sm font-medium text-stone-500 hover:text-stone-900 dark:hover:text-stone-300 transition-colors">
                View all →
            </a>
        </div>

        @if($recentLectures->isEmpty())
            <div class="bg-white dark:bg-stone-900 rounded-2xl p-12 border border-stone-200 dark:border-stone-800 text-center">
                <div class="w-14 h-14 rounded-2xl bg-stone-100 dark:bg-stone-800 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                    </svg>
                </div>
                <h3 class="font-medium text-stone-900 dark:text-stone-50 mb-2">No lectures yet</h3>
                <p class="text-stone-500 mb-6 max-w-sm mx-auto">Upload your first lecture recording and let AI transform it into smart notes.</p>
                <a href="{{ route('lectures.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-stone-900 dark:bg-stone-100 text-white dark:text-stone-900 rounded-xl text-sm font-medium hover:bg-stone-800 dark:hover:bg-stone-200 transition-colors">
                    Get Started
                </a>
            </div>
        @else
            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($recentLectures as $lecture)
                    @include('lectures.partials.card', ['lecture' => $lecture])
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
