<?php

return [
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
    'timeout' => (int) env('GEMINI_TIMEOUT', 120),
    'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models/',
    'ffmpeg_path' => 'C:\\ffmpeg\\ffmpeg-master-latest-win64-gpl\\bin\\ffmpeg.exe',

    // Danh sach fallback models - he thong se tu dong thu khi model chinh loi
    // (them/sua trong file .env: GEMINI_FALLBACK_MODELS=...)
    'fallback_models' => array_filter(array_map('trim', explode(',', (string) env('GEMINI_FALLBACK_MODELS', 'gemini-2.0-flash-lite,gemini-2.5-flash-lite,gemini-2.0-flash,gemini-2.5-flash')))),

    // Groq API - backup provider (mien phi, on dinh hon Gemini free tier)
    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        'endpoint' => 'https://api.groq.com/openai/v1/chat/completions',
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'whisper_model' => env('OPENAI_WHISPER_MODEL', 'whisper-1'),
    ],

    'audio' => [
        'max_size_kb' => (int) env('AUDIO_MAX_SIZE_KB', 51200),
        'allowed_mimes' => [
            'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/x-wav',
            'audio/webm', 'video/webm', 'audio/ogg',
            'audio/mp4', 'audio/x-m4a', 'audio/m4a',
            'application/octet-stream'
        ],
        'allowed_extensions' => ['mp3', 'wav', 'm4a', 'ogg', 'webm', 'mp4'],
    ],
];