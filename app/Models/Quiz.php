<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory, HasJsonTranslations;

    protected $fillable = [
        'code',
        'course_id',
        'chapter_id',
        'type',
        'title',
        'subtitle',
        'description',
        'passing_score',
        'time_limit_minutes',
        'max_attempts',
        'sort_order',
        'is_published',
    ];

    public array $translatable = ['title', 'subtitle', 'description'];

    protected function casts(): array
    {
        return [
            'title'        => 'array',
            'subtitle'     => 'array',
            'description'  => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }

    public function passages(): HasMany
    {
        return $this->hasMany(Passage::class)->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function totalPoints(): int
    {
        return (int) $this->questions()->sum('points');
    }

    public function isFinalExam(): bool
    {
        return $this->type === 'final';
    }

    public function isChapterQuiz(): bool
    {
        return $this->type === 'chapter';
    }

    public function isPassedBy(?User $user): bool
    {
        if (! $user) return false;
        return $this->attempts()
            ->where('user_id', $user->id)
            ->where('passed', true)
            ->exists();
    }

    public function scopeChapterQuizzes($query)
    {
        return $query->where('type', 'chapter');
    }

    public function scopeFinalExams($query)
    {
        return $query->where('type', 'final');
    }
}
