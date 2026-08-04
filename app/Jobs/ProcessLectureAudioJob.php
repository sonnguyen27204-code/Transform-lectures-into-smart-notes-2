<?php

namespace App\Jobs;

use App\Models\Lecture;
use App\Services\AudioProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessLectureAudioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 600; // 10 phút

    public function __construct(public int $lectureId)
    {
    }

    public function handle(AudioProcessingService $processor): void
    {
        $lecture = Lecture::find($this->lectureId);

        if (!$lecture) {
            Log::warning("ProcessLectureAudioJob: Lecture {$this->lectureId} not found");
            return;
        }

        $processor->process($lecture);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessLectureAudioJob failed for lecture {$this->lectureId}", [
            'error' => $exception->getMessage(),
        ]);
    }
}