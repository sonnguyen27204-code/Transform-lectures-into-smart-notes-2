<?php

namespace App\Console\Commands;

use App\Services\GeminiService;
use App\Services\GroqService;
use Illuminate\Console\Command;

class CheckAiProviders extends Command
{
    protected $signature = 'ai:check';
    protected $description = 'Kiem tra trang thai cac AI providers (Gemini, Groq, OpenAI)';

    public function handle(): int
    {
        $this->info('=== Kiem tra AI Providers ===' . PHP_EOL);

        // 1. Gemini
        $this->info(PHP_EOL . '--- 1. Gemini ---');
        $apiKey = config('gemini.api_key');
        $models = config('gemini.fallback_models', []);

        if (empty($apiKey)) {
            $this->error('GEMINI_API_KEY chua duoc cau hinh');
        } else {
            $this->info('API Key: ' . substr($apiKey, 0, 12) . '...');
            $this->info('Fallback models: ' . implode(', ', $models));

            try {
                $service = new GeminiService();
                $service->validateApiKey();
                $this->info('✓ Gemini san sang!');
            } catch (\Throwable $e) {
                $this->warn('Gemini co loi: ' . $e->getMessage());
            }
        }

        // 2. Groq
        $this->info(PHP_EOL . '--- 2. Groq ---');
        $groq = new GroqService();
        if ($groq->isConfigured()) {
            $this->info('Groq API key: ' . substr(config('gemini.groq.api_key'), 0, 12) . '...');
            $this->info('Model: ' . config('gemini.groq.model'));
            $this->info('✓ Groq san sang (backup)');
        } else {
            $this->warn('Chua co GROQ_API_KEY');
            $this->line('  → Dang ky mien phi tai: https://console.groq.com/keys');
            $this->line('  → Them vao .env: GROQ_API_KEY=gsk_...');
        }

        // 3. OpenAI Whisper
        $this->info(PHP_EOL . '--- 3. OpenAI Whisper (backup transcription) ---');
        $openaiKey = config('gemini.openai.api_key');
        if (!empty($openaiKey)) {
            $this->info('OpenAI key: ' . substr($openaiKey, 0, 12) . '...');
            $this->info('Whisper model: ' . config('gemini.openai.whisper_model'));
            $this->info('✓ Whisper san sang');
        } else {
            $this->warn('Chua co OPENAI_API_KEY');
            $this->line('  → Khong co Whisper backup. Chi co The xu ly qua Gemini.');
        }

        // Summary
        $this->info(PHP_EOL . '=== Tom lai ===');
        $hasGemini = !empty($apiKey);
        $hasGroq = $groq->isConfigured();
        $hasWhisper = !empty($openaiKey);

        if ($hasGemini) {
            $this->info('✓ Gemini: xu ly chinh (multi-model fallback)');
        } else {
            $this->error('✗ Gemini: khong kha dung');
        }

        if ($hasWhisper && $hasGroq) {
            $this->info('✓ Whisper + Groq: backup full pipeline');
        } elseif ($hasGroq) {
            $this->warn('⚠ Groq: co nhung CAN Whisper de lay transcript');
        } else {
            $this->warn('✗ Khong co backup provider');
        }

        return self::SUCCESS;
    }
}