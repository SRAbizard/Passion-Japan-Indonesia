<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One photo / video inside a Gallery (album). Type is image | video |
 * youtube; only the matching media column is populated.
 */
#[Fillable([
    'gallery_id', 'type',
    'image_path', 'video_path', 'youtube_url',
    'caption', 'sort_order', 'is_published',
])]
class GalleryItem extends Model
{
    use HasJsonTranslations;

    public array $translatable = ['caption'];

    protected function casts(): array
    {
        return [
            'caption'      => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->video_path ? asset('storage/'.$this->video_path) : null;
    }

    /**
     * Best-effort thumbnail for grid cards.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->image_path) return $this->image_url;
        if ($this->type === 'youtube' && $this->youtube_url) {
            if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([\w-]{11})~', $this->youtube_url, $m)) {
                return "https://img.youtube.com/vi/{$m[1]}/hqdefault.jpg";
            }
        }
        return null;
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (! $this->youtube_url) return null;
        if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([\w-]{11})~', $this->youtube_url, $m)) {
            return "https://www.youtube.com/embed/{$m[1]}?rel=0";
        }
        return $this->youtube_url;
    }
}
