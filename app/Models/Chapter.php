<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

#[Fillable(['course_id', 'title', 'description', 'sort_order', 'is_published', 'unlock_mode'])]
class Chapter extends Model
{
    use HasJsonTranslations;

    public const UNLOCK_FREE       = 'free';
    public const UNLOCK_SEQUENTIAL = 'sequential';

    public array $translatable = ['title', 'description'];

    protected function casts(): array
    {
        return [
            'title'        => 'array',
            'description'  => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function materials(): HasMany { return $this->hasMany(Material::class)->orderBy('sort_order'); }

    /**
     * All quizzes attached to this chapter (post-restructure: many allowed).
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class)->where('type', 'chapter')->orderBy('sort_order');
    }

    /**
     * Unified timeline used by the curriculum sidebar: materials + chapter
     * quizzes merged and sorted by sort_order. Each entry has a normalised
     * shape so the view doesn't need polymorphism gymnastics.
     */
    public function items(): Collection
    {
        $materials = $this->materials->map(fn ($m) => (object) [
            'kind'        => 'material',
            'model'       => $m,
            'id'          => $m->id,
            'code'        => $m->code,
            'title'       => $m->t('title'),
            'badge'       => match ($m->type) {
                'video' => 'Video Lesson',
                'embed' => 'Lesson',
                'pdf'   => 'PDF Lesson',
                default => 'Text Lesson',
            },
            'sort_order'  => $m->sort_order,
        ]);

        $quizzes = $this->quizzes->map(fn ($q) => (object) [
            'kind'        => 'quiz',
            'model'       => $q,
            'id'          => $q->id,
            'code'        => $q->code,
            'title'       => $q->t('title'),
            'badge'       => trans_choice('{1} :count question|[2,*] :count questions',
                                $q->questions_count ?? $q->questions()->count(),
                                ['count' => $q->questions_count ?? $q->questions()->count()]),
            'sort_order'  => $q->sort_order,
        ]);

        return $materials->concat($quizzes)->sortBy('sort_order')->values();
    }

    public function isSequential(): bool
    {
        return $this->unlock_mode === self::UNLOCK_SEQUENTIAL;
    }

    /**
     * Decorate each item in $this->items() with `done` and `locked` flags
     * for a given user. Lock logic:
     *   - free chapter        → nothing is locked
     *   - sequential chapter  → item locked unless ALL previous items in the
     *                           same chapter are done
     */
    public function itemsFor(?User $user): Collection
    {
        $items = $this->items();
        if ($items->isEmpty()) return $items;

        $completedMaterialIds = $user
            ? LessonProgress::where('user_id', $user->id)
                ->whereIn('material_id', $items->where('kind', 'material')->pluck('id'))
                ->whereNotNull('completed_at')
                ->pluck('material_id')->all()
            : [];

        $passedQuizIds = $user
            ? QuizAttempt::where('user_id', $user->id)
                ->whereIn('quiz_id', $items->where('kind', 'quiz')->pluck('id'))
                ->where('passed', true)
                ->pluck('quiz_id')->all()
            : [];

        $allPrevDone = true;
        return $items->map(function ($it) use ($completedMaterialIds, $passedQuizIds, &$allPrevDone) {
            $it->done = $it->kind === 'material'
                ? in_array($it->id, $completedMaterialIds, true)
                : in_array($it->id, $passedQuizIds, true);
            $it->locked = $this->isSequential() && ! $allPrevDone;
            if (! $it->done) $allPrevDone = false;
            return $it;
        });
    }

    /**
     * Quick progress tuple ['done' => int, 'total' => int] for the curriculum
     * sidebar "X/Y" badge next to each chapter title.
     */
    public function progressFor(?User $user): array
    {
        $items = $this->itemsFor($user);
        return [
            'done'  => $items->where('done', true)->count(),
            'total' => $items->count(),
        ];
    }
}
