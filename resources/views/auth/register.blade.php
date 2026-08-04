@extends('layouts.guest')

@section('title', 'Register')

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
                Start creating<br>smarter notes.
            </h2>
            <p class="text-stone-400 text-lg mb-12">
                Join thousands of students transforming how they learn with AI-powered lecture notes.
            </p>

            <div class="grid grid-cols-3 gap-4">
                <div class="text-center p-4 bg-stone-800/50 rounded-xl">
                    <div class="text-3xl font-semibold text-white">10s</div>
                    <div class="text-xs text-stone-500 mt-1">Upload</div>
                </div>
                <div class="text-center p-4 bg-stone-800/50 rounded-xl">
                    <div class="text-3xl font-semibold text-white">30s</div>
                    <div class="text-xs text-stone-500 mt-1">Process</div>
                </div>
                <div class="text-center p-4 bg-stone-800/50 rounded-xl">
                    <div class="text-3xl font-semibold text-white">100%</div>
                    <div class="text-xs text-stone-500 mt-1">Vietnamese</div>
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
                <h2 class="text-2xl font-semibold text-stone-900">Create account</h2>
                <p class="text-stone-500 mt-1">Free. No credit card required.</p>
            </div>

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

<x-ui.input
                    label="Name"
                    name="name"
                    type="text"
                    required
                    placeholder="Your name"
                    :value="old('name')"
                />

<x-ui.input
                    label="Email"
                    name="email"
                    type="email"
                    required
                    placeholder="you@example.com"
                    :value="old('email')"
                />

                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-2">Vai trò</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="role" value="student" class="peer sr-only" checked>
                            <div class="p-3 bg-white border-2 border-stone-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-stone-500 peer-checked:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <div>
                                        <div class="font-medium text-stone-900 text-sm">Học sinh</div>
                                        <div class="text-xs text-stone-500">Học và ôn tập</div>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="role" value="teacher" class="peer sr-only">
                            <div class="p-3 bg-white border-2 border-stone-200 rounded-xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-stone-500 peer-checked:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                    </svg>
                                    <div>
                                        <div class="font-medium text-stone-900 text-sm">Giáo viên</div>
                                        <div class="text-xs text-stone-500">Tạo bài giảng</div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

<x-ui.input
                    label="Password"
                    name="password"
                    type="password"
                    required
                    placeholder="Min 6 characters"
                />

<x-ui.input
                    label="Confirm Password"
                    name="password_confirmation"
                    type="password"
                    required
                    placeholder="Repeat password"
                />

                <x-ui.button type="submit" variant="primary" size="lg" class="w-full justify-center">
                    Create account
                </x-ui.button>
            </form>

            <p class="mt-8 text-center text-sm text-stone-500">
                Already have an account?
                <a href="{{ route('login') }}" class="font-medium text-stone-900 hover:text-stone-600">Sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection
