@auth
@php
    $navItems = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['route' => 'lectures.index', 'label' => 'Lectures', 'icon' => 'M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z'],
        ['route' => 'lectures.create', 'label' => 'Upload', 'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12'],
    ];
@endphp

<aside class="fixed left-0 top-0 bottom-0 w-64 bg-stone-900 border-r border-stone-800 hidden lg:flex flex-col z-40">
    {{-- Logo --}}
    <div class="p-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="w-9 h-9 bg-stone-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-stone-900" viewBox="0 0 24 24" fill="currentColor">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="3" y="14" width="18" height="7" rx="1.5"/>
                </svg>
            </div>
            <div>
                <h1 class="font-semibold text-white text-base leading-none">Lectio</h1>
                <p class="text-stone-500 text-xs mt-0.5">AI Notes</p>
            </div>
        </a>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-4 py-2">
        @foreach($navItems as $item)
            @php $active = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 mb-1
                      {{ $active ? 'bg-stone-800 text-white' : 'text-stone-400 hover:text-white hover:bg-stone-800/50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"/>
                </svg>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    {{-- User --}}
    <div class="p-4 border-t border-stone-800">
        <div class="flex items-center gap-3 px-3 py-3 rounded-xl bg-stone-800/50">
            <div class="w-9 h-9 bg-stone-100 rounded-full flex items-center justify-center text-stone-900 font-semibold text-sm">
                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-stone-500 hover:text-stone-300 transition-colors p-1" title="Logout">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
@endauth
