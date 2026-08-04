<div class="mt-6 pt-6 border-t border-stone-200 dark:border-stone-700">
    <p class="text-sm text-stone-500 mb-3">Or record directly:</p>
    <div class="flex flex-wrap items-center gap-3">
        <button type="button" id="btn-record" onclick="toggleRecord()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 font-medium hover:bg-stone-200 dark:hover:bg-stone-700 transition-colors">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="6"/>
            </svg>
            <span id="record-label">Start Recording</span>
        </button>
        <span id="record-timer" class="text-sm font-mono text-stone-500 hidden">00:00</span>
    </div>
    <div id="record-status" class="hidden mt-3 text-xs text-stone-500"></div>
</div>