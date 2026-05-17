<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Reading passage (Dokkai). Belongs to one quiz, can be referenced by
 * many quiz questions.
 */
class Passage extends Model
{
    use HasFactory, HasJsonTranslations;

    protected $fillable = [
        'quiz_id',
        'title',
        'content',
        'translation',
        'sort_order',
    ];

    public array $translatable = ['title', 'content', 'translation'];

    protected function casts(): array
    {
        return [
            'title'       => 'array',
            'content'     => 'array',
            'translation' => 'array',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }
}
