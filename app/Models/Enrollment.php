<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'course_id', 'status', 'progress_pct', 'started_at', 'last_activity_at', 'completed_at', 'expires_at'])]
class Enrollment extends Model
{
    public const STATUSES = ['enrolled', 'in_progress', 'completed', 'expired', 'dropped'];

    public const STATUS_COLORS = [
        'enrolled'    => 'gray',
        'in_progress' => 'info',
        'completed'   => 'success',
        'expired'     => 'warning',
        'dropped'     => 'danger',
    ];

    protected function casts(): array
    {
        return [
            'started_at'       => 'datetime',
            'last_activity_at' => 'datetime',
            'completed_at'     => 'datetime',
            'expires_at'       => 'datetime',
        ];
    }

    public function user(): BelongsTo   { return $this->belongsTo(User::class); }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }

    public function scopeActive(Builder $q): Builder
    {
        return $q->whereIn('status', ['enrolled', 'in_progress']);
    }

    /**
     * Recompute progress based on lesson_progress vs total materials in course.
     */
    public function recomputeProgress(): void
    {
        $totalMaterials = $this->course->materials()->count();

        if ($totalMaterials === 0) {
            $this->progress_pct = 0;
            $this->save();
            return;
        }

        $completed = LessonProgress::query()
            ->where('user_id', $this->user_id)
            ->whereIn('material_id', $this->course->materials()->pluck('materials.id'))
            ->whereNotNull('completed_at')
            ->count();

        $pct = (int) round($completed / $totalMaterials * 100);

        $this->forceFill([
            'progress_pct'     => $pct,
            'status'           => $pct === 0 ? 'enrolled' : ($pct >= 100 ? 'completed' : 'in_progress'),
            'last_activity_at' => now(),
            'started_at'       => $this->started_at ?: ($pct > 0 ? now() : null),
            'completed_at'     => $pct >= 100 && ! $this->completed_at ? now() : $this->completed_at,
        ])->save();
    }
}
