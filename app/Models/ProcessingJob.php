<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessingJob extends Model
{
    use HasFactory;

    protected $table = 'processing_jobs';

    protected $fillable = [
        'lecture_id',
        'stage',
        'progress',
        'message',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function lecture(): BelongsTo
    {
        return $this->belongsTo(Lecture::class);
    }

    public function getStageLabel(): string
    {
        return match ($this->stage) {
            'uploading' => 'Tải lên',
            'transcribing' => 'Chuyển giọng nói',
            'analyzing' => 'Phân tích nội dung',
            'generating' => 'Tạo quiz & flashcard',
            'completed' => 'Hoàn thành',
            'failed' => 'Thất bại',
            default => 'Đang xử lý',
        };
    }
}