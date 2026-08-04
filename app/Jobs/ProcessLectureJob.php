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

class ProcessLectureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 600;
    public int $backoff = 60;

    // Chỉ serialize ID, tránh giữ toàn bộ model lâu trong queue
    public function __construct(public int $lectureId)
    {
    }

    public function handle(AudioProcessingService $processor): void
    {
        $lecture = Lecture::find($this->lectureId);

        if (!$lecture) {
            Log::warning("ProcessLectureJob: Lecture {$this->lectureId} not found");
            return;
        }

        Log::info('Queue: Processing lecture', [
            'id' => $lecture->id,
            'title' => $lecture->title,
            'attempt' => $this->attempts(),
        ]);

        try {
            $processor->process($lecture);
        } catch (\Throwable $e) {
            Log::error('Queue: Processing failed', [
                'lecture_id' => $lecture->id,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $lecture = Lecture::find($this->lectureId);

        if ($lecture) {
            $lecture->update([
                'status' => 'failed',
                'error_message' => 'Đã thử ' . $this->tries . ' lần: ' . $exception->getMessage(),
            ]);
        }

        Log::critical('Queue: Job failed permanently', [
            'lecture_id' => $this->lectureId,
            'error' => $exception->getMessage(),
        ]);
    }
}