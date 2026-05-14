<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'job_vacancy_id', 'status', 'cover_letter', 'admin_notes', 'reviewed_at', 'reviewed_by'])]
class Application extends Model
{
    public const STATUSES = [
        'submitted',
        'under_review',
        'interview_scheduled',
        'offered',
        'accepted',
        'rejected',
        'withdrawn',
    ];

    public const STATUS_COLORS = [
        'submitted'           => 'gray',
        'under_review'        => 'info',
        'interview_scheduled' => 'warning',
        'offered'             => 'primary',
        'accepted'            => 'success',
        'rejected'            => 'danger',
        'withdrawn'           => 'gray',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class, 'job_vacancy_id');
    }

    /** Alias for vacancy() — easier-to-read in student-facing code. */
    public function jobVacancy(): BelongsTo
    {
        return $this->vacancy();
    }

    protected static function booted(): void
    {
        static::updated(function (self $app) {
            if ($app->wasChanged('status') && $app->user) {
                $old = $app->getOriginal('status');
                $app->user->notify(new \App\Notifications\ApplicationStatusChanged($app, $old, $app->status));
            }
        });
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->whereNotIn('status', ['rejected', 'withdrawn']);
    }

    public function getStatusLabelAttribute(): string
    {
        return __('application.status.'.$this->status);
    }
}
