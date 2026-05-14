<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['chapter_id', 'title', 'type', 'video_url', 'pdf_path', 'content', 'duration_minutes', 'sort_order', 'is_free_preview'])]
class Material extends Model
{
    use HasJsonTranslations;

    public array $translatable = ['title', 'content'];

    protected function casts(): array
    {
        return [
            'title'           => 'array',
            'content'         => 'array',
            'is_free_preview' => 'boolean',
        ];
    }

    public function chapter(): BelongsTo { return $this->belongsTo(Chapter::class); }
    public function progresses(): HasMany { return $this->hasMany(LessonProgress::class); }

    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path ? asset('storage/'.$this->pdf_path) : null;
    }

    public function isCompletedBy(?User $user): bool
    {
        if (! $user) return false;
        return $this->progresses()->where('user_id', $user->id)->whereNotNull('completed_at')->exists();
    }
}
