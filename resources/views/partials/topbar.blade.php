@auth
<header class="sticky top-0 z-30 bg-stone-50/80 dark:bg-stone-950/80 backdrop-blur-xl border-b border-stone-200 dark:border-stone-800">
    <div class="flex items-center justify-between h-16 px-6 lg:pl-72">
        {{-- Mobile menu --}}
        <button class="lg:hidden p-2 -ml-2 text-stone-500 hover:text-stone-900 dark:hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Search --}}
        <div class="hidden sm:flex flex-1 max-w-md">
            <div class="relative w-full">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" placeholder="Search lectures..."
                       class="w-full pl-10 pr-4 py-2 bg-stone-100/50 dark:bg-stone-900/50 border-0 rounded-xl text-sm text-stone-900 dark:text-stone-100 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-stone-900/10 dark:focus:ring-white/10">
            </div>
        </div>

        {{-- Right --}}
        <div class="flex items-center gap-3">
            @hasSection('page_actions')
                <div class="flex items-center gap-2">
                    @yield('page_actions')
                </div>
            @endif
        </div>
    </div>
</header>
@endauth
