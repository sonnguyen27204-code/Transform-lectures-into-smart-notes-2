<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Summary extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecture_id',
        'brief',
        'key_takeaways',
        'topics',
        'sentiment',
    ];

    protected function casts(): array
    {
        return [
            'key_takeaways' => 'array',
            'topics' => 'array',
        ];
    }

    public function lecture(): BelongsTo
    {
        return $this->belongsTo(Lecture::class);
    }
}