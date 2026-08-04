@auth
<footer class="px-6 py-6 mt-auto border-t border-stone-200 dark:border-stone-800">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-stone-400">
        <div>
            © {{ date('Y') }} Lectio
        </div>
        <div class="flex items-center gap-4">
            <span>Powered by Gemini AI</span>
            <span class="hidden sm:inline">·</span>
            <span class="hidden sm:inline">Laravel {{ app()->version() }}</span>
        </div>
    </div>
</footer>
@endauth
