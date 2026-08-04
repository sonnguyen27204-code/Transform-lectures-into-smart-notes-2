<?php

namespace App\Console\Commands;

use App\Services\GeminiService;
use Illuminate\Console\Command;

class CheckGeminiApi extends Command
{
    protected $signature = 'gemini:check {--fix : Tu dong sua loi neu co}';
    protected $description = 'Kiem tra trang thai GEMINI API Key';

    public function handle(): int
    {
        $this->info('=== Kiem tra GEMINI API ===' . PHP_EOL);

        // 1. Kiem tra config
        $apiKey = config('gemini.api_key');
        $model = config('gemini.model');

        $this->info("Model: {$model}");
        
        if (empty($apiKey)) {
            $this->error('GEMINI_API_KEY chua duoc cau hinh!');
            $this->line('Vui long them vao file .env:');
            $this->line('GEMINI_API_KEY=your_api_key_here');
            return self::FAILURE;
        }

        $this->info("API Key: " . substr($apiKey, 0, 10) . '...' . substr($apiKey, -5));

        // 2. Test API
        $this->info(PHP_EOL . 'Dang kiem tra ket noi...');

        try {
            $service = new GeminiService();
            $service->validateApiKey();
            $this->info('✓ API Key hop le!');
            $this->info('✓ San sang xu ly audio!');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('✗ Loi API: ' . $e->getMessage());
            
            // Auto fix common issues
            if ($this->option('fix')) {
                $this->fixCommonIssues($e->getMessage());
            }
            
            return self::FAILURE;
        }
    }

    protected function fixCommonIssues(string $errorMessage): void
    {
        $this->line(PHP_EOL . '=== Dac biet goi y sua loi ===');

        if (str_contains($errorMessage, 'gemini-3.5')) {
            $this->warn('Model name sai. Sua thanh gemini-1.5-flash...');
            $envFile = base_path('.env');
            $content = file_get_contents($envFile);
            
            if (preg_match('/GEMINI_MODEL=(.+)/', $content, $matches)) {
                $content = str_replace($matches[0], 'GEMINI_MODEL=gemini-1.5-flash', $content);
                file_put_contents($envFile, $content);
                $this->info('Da sua .env - Vui long chay lai lenh nay');
            }
        }
    }
}
