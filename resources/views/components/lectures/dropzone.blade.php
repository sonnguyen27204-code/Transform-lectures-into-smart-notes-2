@props([
    'name' => 'audio',
    'accept' => 'audio/*',
    'maxKb' => 51200,
])

<div>
    <div id="dropzone"
         class="relative border-2 border-dashed border-stone-300 dark:border-stone-600 rounded-2xl p-10 text-center bg-stone-50 dark:bg-stone-800/50 hover:border-stone-400 dark:hover:border-stone-500 hover:bg-stone-100 dark:hover:bg-stone-800 transition-all cursor-pointer"
         onclick="document.getElementById('audio-input').click()">
        <input type="file" id="audio-input" name="{{ $name }}" accept="{{ $accept }}" class="hidden" required>

        <div id="dropzone-empty">
            <div class="w-14 h-14 rounded-2xl bg-stone-200 dark:bg-stone-700 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-stone-500 dark:text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
            </div>
            <p class="font-medium text-stone-900 dark:text-stone-100 mb-1">Drop file here</p>
            <p class="text-sm text-stone-500 mb-3">or <span class="text-stone-700 dark:text-stone-300 font-medium">click to browse</span></p>
            <p class="text-xs text-stone-400">MP3, WAV, M4A, OGG, WebM · Max {{ round($maxKb / 1024, 1) }} MB</p>
        </div>

        <div id="dropzone-filled" class="hidden">
            <div class="flex items-center gap-4 max-w-sm mx-auto">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/>
                    </svg>
                </div>
                <div class="flex-1 text-left min-w-0">
                    <p id="file-name" class="font-medium text-stone-900 dark:text-stone-100 truncate"></p>
                    <p id="file-meta" class="text-xs text-stone-500"></p>
                </div>
                <button type="button" onclick="event.stopPropagation(); resetFile()" class="p-2 rounded-lg text-stone-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
            <div id="audio-preview" class="mt-4">
                <audio id="preview-player" controls class="w-full max-w-xs mx-auto"></audio>
            </div>
        </div>
    </div>
</div>