<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'slug', 'course_category_id', 'instructor_id',
    'title', 'subtitle', 'description', 'what_you_learn', 'prerequisites',
    'thumbnail_path', 'intro_video_url',
    'level', 'duration_days',
    'price', 'currency', 'is_free',
    'is_featured', 'published_at',
])]
class Course extends Model
{
    use HasJsonTranslations;

    public array $translatable = ['title', 'subtitle', 'description', 'what_you_learn', 'prerequisites'];

    protected function casts(): array
    {
        return [
            'title'          => 'array',
            'subtitle'       => 'array',
            'description'    => 'array',
            'what_you_learn' => 'array',
            'prerequisites'  => 'array',
            'is_free'        => 'boolean',
            'is_featured'    => 'boolean',
            'published_at'   => 'datetime',
        ];
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function category(): BelongsTo  { return $this->belongsTo(CourseCategory::class, 'course_category_id'); }
    public function instructor(): BelongsTo { return $this->belongsTo(User::class, 'instructor_id'); }
    public function chapters(): HasMany    { return $this->hasMany(Chapter::class)->orderBy('sort_order'); }
    public function enrollments(): HasMany { return $this->hasMany(Enrollment::class); }

    public function materials(): HasManyThrough
    {
        return $this->hasManyThrough(Material::class, Chapter::class);
    }

    /**
     * The final-exam quiz for this course (type=final). Used by the
     * student certificate flow — kept under the name `quiz()` for
     * backward compatibility with the existing ElearningController.
     */
    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class)
            ->where('is_published', true)
            ->where('type', 'final');
    }

    public function finalExam(): HasOne
    {
        return $this->quiz();
    }

    /**
     * HasMany variant used by the Filament FinalExamRelationManager,
     * which needs to render a table (even if it usually has only 0 or 1
     * row) and call CRUD actions on the relation.
     */
    public function finalExams(): HasMany
    {
        return $this->hasMany(Quiz::class)->where('type', 'final');
    }

    /**
     * Per-chapter quizzes (one per bab).
     */
    public function chapterQuizzes(): HasMany
    {
        return $this->hasMany(Quiz::class)->where('type', 'chapter');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function hasQuiz(): bool
    {
        return $this->quiz()->exists();
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? asset('storage/'.$this->thumbnail_path) : null;
    }

    public function getMaterialsCountAttribute(): int
    {
        return $this->materials()->count();
    }

    public function getPriceDisplayAttribute(): string
    {
        if ($this->is_free) return __('Free');
        return ($this->currency ?: 'IDR').' '.number_format($this->price, 0, ',', '.');
    }
}
