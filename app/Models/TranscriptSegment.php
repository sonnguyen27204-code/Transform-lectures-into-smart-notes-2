<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranscriptSegment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transcript_id',
        'start_time',
        'end_time',
        'text',
        'speaker',
        'speaker_label',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'float',
            'end_time' => 'float',
        ];
    }

    public function transcript(): BelongsTo
    {
        return $this->belongsTo(Transcript::class);
    }

    public function getFormattedStart(): string
    {
        return $this->formatTime($this->start_time);
    }

    public function getFormattedEnd(): string
    {
        return $this->formatTime($this->end_time);
    }

    private function formatTime(float $seconds): string
    {
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = floor($seconds % 60);
        if ($h > 0) {
            return sprintf('%d:%02d:%02d', $h, $m, $s);
        }
        return sprintf('%d:%02d', $m, $s);
    }

    public function getSpeakerColor(): string
    {
        return match ($this->speaker) {
            'teacher' => 'brand',
            'student' => 'accent',
            default => 'slate',
        };
    }
}