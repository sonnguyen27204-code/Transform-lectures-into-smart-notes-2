<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Lecture extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'audio_path',
        'audio_url',
        'original_filename',
        'mime_type',
        'file_size',
        'duration',
        'status',
        'language',
        'error_message',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'file_size' => 'integer',
            'processed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transcript(): HasOne
    {
        return $this->hasOne(Transcript::class);
    }

    public function summary(): HasOne
    {
        return $this->hasOne(Summary::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class);
    }

    public function processingJobs(): HasMany
    {
        return $this->hasMany(ProcessingJob::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return in_array($this->status, ['uploading', 'transcribing', 'analyzing', 'generating'], true);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Chờ xử lý',
            'uploading' => 'Đang tải lên',
            'transcribing' => 'Đang chuyển giọng nói',
            'analyzing' => 'Đang phân tích',
            'generating' => 'Đang tạo nội dung',
            'completed' => 'Hoàn thành',
            'failed' => 'Thất bại',
            default => 'Không xác định',
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            'completed' => 'emerald',
            'failed' => 'rose',
            'pending' => 'slate',
            default => 'amber',
        };
    }

    public function getAudioStreamUrl(): string
    {
        if ($this->audio_path && Storage::disk('public')->exists($this->audio_path)) {
            return Storage::disk('public')->url($this->audio_path);
        }
        return $this->audio_url ?? '';
    }

    public function getDurationFormatted(): string
    {
        $seconds = $this->duration ?? 0;
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = $seconds % 60;
        if ($h > 0) {
            return sprintf('%d:%02d:%02d', $h, $m, $s);
        }
        return sprintf('%d:%02d', $m, $s);
    }
}