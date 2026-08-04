@extends('layouts.app')

@section('title', 'Lectures')

@section('page_actions')
    <a href="{{ route('lectures.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-stone-900 dark:bg-stone-100 text-white dark:text-stone-900 rounded-xl text-sm font-medium hover:bg-stone-800 dark:hover:bg-stone-200 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Upload
    </a>
@endsection

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-semibold text-stone-900 dark:text-stone-50">Lectures</h1>
        <p class="text-stone-500 mt-1">{{ $lectures->total() }} total lectures</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-stone-900 rounded-2xl border border-stone-200 dark:border-stone-800 p-4">
        <form method="GET" action="{{ route('lectures.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search lectures..."
                       class="w-full pl-10 pr-4 py-2.5 bg-stone-50 dark:bg-stone-800 border-0 rounded-xl text-sm text-stone-900 dark:text-stone-100 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-stone-900/10">
            </div>

            <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2.5 bg-stone-50 dark:bg-stone-800 border-0 rounded-xl text-sm text-stone-900 dark:text-stone-100 focus:outline-none focus:ring-2 focus:ring-stone-900/10 cursor-pointer">
                <option value="">All status</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="transcribing" @selected(request('status') === 'transcribing')>Transcribing</option>
                <option value="analyzing" @selected(request('status') === 'analyzing')>Analyzing</option>
                <option value="generating" @selected(request('status') === 'generating')>Generating</option>
                <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                <option value="failed" @selected(request('status') === 'failed')>Failed</option>
            </select>

            @if(request()->hasAny(['q', 'status']))
                <a href="{{ route('lectures.index') }}" class="px-4 py-2.5 bg-stone-100 dark:bg-stone-800 text-stone-600 dark:text-stone-400 rounded-xl text-sm font-medium hover:bg-stone-200 dark:hover:bg-stone-700 transition-colors inline-flex items-center justify-center">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Grid --}}
    @if($lectures->isEmpty())
        <div class="bg-white dark:bg-stone-900 rounded-2xl border border-stone-200 dark:border-stone-800 p-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-stone-100 dark:bg-stone-800 flex items-center justify-center mx-auto mb-5">
                <svg class="w-7 h-7 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                </svg>
            </div>
            <h3 class="font-medium text-stone-900 dark:text-stone-50 mb-2">No lectures found</h3>
            <p class="text-stone-500 mb-6">
                @if(request()->hasAny(['q', 'status']))
                    Try adjusting your search or filters.
                @else
                    Upload your first lecture to get started.
                @endif
            </p>
            <a href="{{ route('lectures.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-stone-900 dark:bg-stone-100 text-white dark:text-stone-900 rounded-xl text-sm font-medium hover:bg-stone-800 dark:hover:bg-stone-200 transition-colors">
                Upload Lecture
            </a>
        </div>
    @else
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($lectures as $lecture)
                @include('lectures.partials.card', ['lecture' => $lecture])
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $lectures->links() }}
        </div>
    @endif
</div>
@endsection
