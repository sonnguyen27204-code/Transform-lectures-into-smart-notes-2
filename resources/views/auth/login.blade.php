@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex">

    {{-- Left --}}
    <div class="hidden lg:flex lg:w-1/2 bg-stone-900 items-center justify-center p-12">
        <div class="max-w-md">
            <div class="flex items-center gap-3 mb-12">
                <div class="w-10 h-10 bg-stone-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-stone-900" viewBox="0 0 24 24" fill="currentColor">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="18" height="7" rx="1.5"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-semibold text-white text-lg leading-none">Lectio</h1>
                    <p class="text-stone-500 text-xs mt-0.5">AI Notes</p>
                </div>
            </div>

            <h2 class="text-4xl font-semibold text-white mb-4 leading-tight">
                Welcome back.
            </h2>
            <p class="text-stone-400 text-lg mb-12">
                Sign in to access your lecture notes and continue learning.
            </p>

            <div class="space-y-4">
                <div class="flex items-center gap-4 p-4 bg-stone-800/50 rounded-xl">
                    <div class="w-10 h-10 bg-stone-100 rounded-lg flex items-center justify-center text-stone-900 font-semibold">1</div>
                    <span class="text-stone-300">Upload lectures with AI transcription</span>
                </div>
                <div class="flex items-center gap-4 p-4 bg-stone-800/50 rounded-xl">
                    <div class="w-10 h-10 bg-stone-100 rounded-lg flex items-center justify-center text-stone-900 font-semibold">2</div>
                    <span class="text-stone-300">Get instant summaries and quizzes</span>
                </div>
                <div class="flex items-center gap-4 p-4 bg-stone-800/50 rounded-xl">
                    <div class="w-10 h-10 bg-stone-100 rounded-lg flex items-center justify-center text-stone-900 font-semibold">3</div>
                    <span class="text-stone-300">Review with flashcards and timestamps</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Right --}}
    <div class="flex-1 flex items-center justify-center p-6 sm:p-12 bg-stone-50">
        <div class="w-full max-w-md">

            <a href="/" class="lg:hidden flex items-center gap-3 mb-8">
                <div class="w-10 h-10 bg-stone-900 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="18" height="7" rx="1.5"/>
                    </svg>
                </div>
                <span class="font-semibold text-stone-900 text-lg">Lectio</span>
            </a>

            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-stone-900">Sign in</h2>
                <p class="text-stone-500 mt-1">Enter your credentials to continue</p>
            </div>

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

<x-ui.input
                    label="Email"
                    name="email"
                    type="email"
                    :value="old('email')"
                    required
                    placeholder="you@example.com"
                />

<x-ui.input
                    label="Password"
                    name="password"
                    type="password"
                    required
                    placeholder="••••••••"
                />

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-stone-300 text-stone-900 focus:ring-stone-900">
                        <span class="text-stone-600">Remember me</span>
                    </label>
                </div>

                <x-ui.button type="submit" variant="primary" size="lg" class="w-full justify-center">
                    Sign in
                </x-ui.button>
            </form>

            <p class="mt-8 text-center text-sm text-stone-500">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-medium text-stone-900 hover:text-stone-600">Create one</a>
            </p>
        </div>
    </div>
</div>
@endsection
