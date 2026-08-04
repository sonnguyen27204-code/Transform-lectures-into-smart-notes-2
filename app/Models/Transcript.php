<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transcript extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecture_id',
        'full_text',
        'language',
        'word_count',
        'confidence',
    ];

    protected function casts(): array
    {
        return [
            'word_count' => 'integer',
            'confidence' => 'float',
        ];
    }

    public function lecture(): BelongsTo
    {
        return $this->belongsTo(Lecture::class);
    }

    public function segments(): HasMany
    {
        return $this->hasMany(TranscriptSegment::class)->orderBy('start_time');
    }
}