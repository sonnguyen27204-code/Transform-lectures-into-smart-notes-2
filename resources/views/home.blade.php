@extends('layouts.guest')

@section('title', 'Lectio — Smart Lecture Notes')

@section('content')
<div class="min-h-screen bg-stone-50">

    {{-- Header --}}
    <header class="border-b border-stone-200">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <div class="w-9 h-9 bg-stone-900 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="18" height="7" rx="1.5"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-semibold text-stone-900 text-base leading-none">Lectio</h1>
                    <p class="text-stone-500 text-xs mt-0.5">AI Notes</p>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-stone-600">
                <a href="#features" class="hover:text-stone-900 transition-colors">Features</a>
                <a href="#how" class="hover:text-stone-900 transition-colors">How it works</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-stone-600 hover:text-stone-900 transition-colors">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-stone-600 hover:text-stone-900 transition-colors">Sign in</a>
                    <a href="{{ route('register') }}" class="px-4 py-2.5 text-sm font-medium bg-stone-900 text-white rounded-xl hover:bg-stone-800 transition-colors">Get started</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="py-24 lg:py-32">
        <div class="max-w-6xl mx-auto px-6">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-semibold text-stone-900 leading-tight">
                    Transform lectures into<br>smart notes
                </h1>
                <p class="text-xl text-stone-500 mt-6 max-w-xl mx-auto leading-relaxed">
                    Upload your lecture recording and get instant transcripts, summaries, and quizzes powered by AI.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-10">
                    @auth
                        <a href="{{ route('lectures.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-stone-900 text-white rounded-xl text-sm font-medium hover:bg-stone-800 transition-colors">
                            Upload a lecture
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-stone-900 text-white rounded-xl text-sm font-medium hover:bg-stone-800 transition-colors">
                            Start for free
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3 text-stone-600 rounded-xl text-sm font-medium hover:text-stone-900 transition-colors">
                            Sign in
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    {{-- Preview --}}
    <section class="py-16">
        <div class="max-w-6xl mx-auto px-6">
            <div class="bg-white rounded-2xl border border-stone-200 shadow-soft-lg overflow-hidden">
                {{-- Browser bar --}}
                <div class="flex items-center gap-2 px-4 py-3 bg-stone-50 border-b border-stone-200">
                    <div class="w-3 h-3 rounded-full bg-stone-300"></div>
                    <div class="w-3 h-3 rounded-full bg-stone-300"></div>
                    <div class="w-3 h-3 rounded-full bg-stone-300"></div>
                    <div class="flex-1 mx-4">
                        <div class="bg-white border border-stone-200 rounded-lg px-3 py-1.5 text-xs text-stone-400 text-center">
                            lectio.app/lectures
                        </div>
                    </div>
                </div>

                {{-- Content --}}
                <div class="grid lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-stone-200">
                    <div class="p-6 lg:col-span-2">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="font-semibold text-stone-900">Lecture 3: Linear Functions</h3>
                                <p class="text-sm text-stone-500 mt-1">Mathematics 9 · 18 minutes · Vietnamese</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                Completed
                            </span>
                        </div>

                        <div class="bg-stone-50 rounded-xl p-4 mb-4">
                            <div class="flex items-center gap-3">
                                <button class="w-10 h-10 bg-stone-900 text-white rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </button>
                                <div class="flex-1 flex items-center gap-0.5 h-8">
                                    @for($i = 0; $i < 50; $i++)
                                        <div class="w-1 bg-stone-300 rounded-full" style="height: {{ rand(20, 100) }}%"></div>
                                    @endfor
                                </div>
                                <span class="text-xs text-stone-500 font-mono">12:34 / 18:20</span>
                            </div>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex gap-3">
                                <span class="text-stone-400 font-mono text-xs mt-0.5">00:42</span>
                                <span class="text-stone-700">Hàm số bậc nhất là hàm có dạng <strong class="text-stone-900">y = ax + b</strong>...</span>
                            </div>
                            <div class="flex gap-3">
                                <span class="text-stone-400 font-mono text-xs mt-0.5">02:15</span>
                                <span class="text-stone-700">Trong đó <strong class="text-stone-900">a ≠ 0</strong> được gọi là hệ số góc...</span>
                            </div>
                            <div class="flex gap-3">
                                <span class="text-stone-400 font-mono text-xs mt-0.5">05:08</span>
                                <span class="text-stone-700">Đồ thị luôn là một đường thẳng...</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-stone-50">
                        <div class="text-xs font-medium text-stone-500 uppercase tracking-wide mb-3">Key Takeaways</div>
                        <ul class="space-y-2 text-sm">
                            <li class="flex gap-2"><span class="text-stone-400">→</span><span class="text-stone-700">Hàm bậc nhất: y = ax + b</span></li>
                            <li class="flex gap-2"><span class="text-stone-400">→</span><span class="text-stone-700">Đồ thị là đường thẳng</span></li>
                            <li class="flex gap-2"><span class="text-stone-400">→</span><span class="text-stone-700">Hệ số góc a quyết định độ dốc</span></li>
                        </ul>

                        <div class="mt-6 pt-6 border-t border-stone-200">
                            <div class="text-xs font-medium text-stone-500 uppercase tracking-wide mb-3">Quiz</div>
                            <div class="bg-white rounded-xl p-4 border border-stone-200">
                                <p class="text-sm font-medium text-stone-900 mb-3">Đồ thị hàm số y = 2x - 1 đi qua điểm nào?</p>
                                <div class="space-y-2">
                                    <div class="text-sm px-3 py-2 rounded-lg bg-stone-100 text-stone-600">A. (0, -1)</div>
                                    <div class="text-sm px-3 py-2 rounded-lg bg-emerald-100 text-emerald-700 border border-emerald-200">B. (1, 1) ✓</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="py-24">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-semibold text-stone-900">Everything you need</h2>
                <p class="text-stone-500 mt-3 max-w-xl mx-auto">Powerful features to help you learn smarter, not harder.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $features = [
                        ['title' => 'Transcript', 'desc' => 'Accurate speech-to-text with timestamps. Vietnamese accented.'],
                        ['title' => 'Summary', 'desc' => 'AI extracts key points and main topics automatically.'],
                        ['title' => 'Quiz', 'desc' => 'Multiple choice questions with answers and explanations.'],
                        ['title' => 'Flashcards', 'desc' => 'Key terms and definitions as interactive flashcards.'],
                        ['title' => 'Timeline', 'desc' => 'Click timestamp to jump to exact audio position.'],
                        ['title' => 'Search', 'desc' => 'Find any word or phrase across all your lectures.'],
                    ];
                @endphp

                @foreach($features as $f)
                    <div class="bg-white rounded-2xl p-6 border border-stone-200">
                        <h3 class="font-semibold text-stone-900 mb-2">{{ $f['title'] }}</h3>
                        <p class="text-sm text-stone-500">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section id="how" class="py-24 bg-white border-t border-stone-200">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-semibold text-stone-900">How it works</h2>
                <p class="text-stone-500 mt-3">Three simple steps to smarter notes.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-12 h-12 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="text-xl font-semibold text-stone-900">1</span>
                    </div>
                    <h3 class="font-semibold text-stone-900 mb-2">Upload</h3>
                    <p class="text-sm text-stone-500">Drop your audio file or record directly. MP3, WAV, M4A, WebM supported.</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="text-xl font-semibold text-stone-900">2</span>
                    </div>
                    <h3 class="font-semibold text-stone-900 mb-2">AI Process</h3>
                    <p class="text-sm text-stone-500">Our AI transcribes, analyzes, and generates notes automatically.</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="text-xl font-semibold text-stone-900">3</span>
                    </div>
                    <h3 class="font-semibold text-stone-900 mb-2">Get Results</h3>
                    <p class="text-sm text-stone-500">View transcript, summary, quiz, and flashcards. Ready in seconds.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-24">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-semibold text-stone-900 mb-4">Ready to start?</h2>
            <p class="text-stone-500 mb-8">Free. No credit card required.</p>

            @auth
                <a href="{{ route('lectures.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-stone-900 text-white rounded-xl text-sm font-medium hover:bg-stone-800 transition-colors">
                    Upload Your First Lecture
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-stone-900 text-white rounded-xl text-sm font-medium hover:bg-stone-800 transition-colors">
                    Create Free Account
                </a>
            @endauth
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-stone-200 py-8">
        <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-stone-900 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="18" height="7" rx="1"/>
                    </svg>
                </div>
                <span class="font-semibold text-stone-900">Lectio</span>
            </div>
            <div class="text-sm text-stone-500">
                © {{ date('Y') }} Lectio · Built with Laravel & Gemini AI
            </div>
        </div>
    </footer>
</div>
@endsection
