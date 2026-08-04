<?php

namespace App\Providers;

use App\Services\AudioProcessingService;
use App\Services\GeminiService;
use App\Services\GroqService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GeminiService::class, function ($app) {
            return new GeminiService();
        });

        $this->app->singleton(GroqService::class, function ($app) {
            return new GroqService();
        });

        $this->app->singleton(AudioProcessingService::class, function ($app) {
            return new AudioProcessingService(
                $app->make(GeminiService::class),
                $app->make(GroqService::class)
            );
        });
    }

    public function boot(): void
    {
        //
    }
}