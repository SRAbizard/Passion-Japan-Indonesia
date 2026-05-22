<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'slug', 'title', 'caption', 'type', 'category',
    'image_path', 'video_path', 'youtube_url',
    'taken_at', 'sort_order', 'is_published',
])]
class Gallery extends Model
{
    use HasJsonTranslations;

    public array $translatable = ['title', 'caption'];

    /**
     * Categories shown as filter tabs on /gallery. Keys are stored in
     * the `category` column; labels go through __() so each locale can
     * translate them in lang/{locale}.json.
     */
    public const CATEGORIES = [
        'mensetsu_offline'    => 'Mensetsu Offline',
        'mensetsu_online'     => 'Mensetsu Online',
        'sosialisasi_kampus'  => 'Sosialisasi Kampus',
        'general'             => 'Umum',
    ];

    public static function categoryOptions(): array
    {
        $out = [];
        foreach (self::CATEGORIES as $key => $label) {
            $out[$key] = __($label);
        }
        return $out;
    }

    public function getCategoryLabelAttribute(): ?string
    {
        if (! $this->category) return __(self::CATEGORIES['general']);
        return __(self::CATEGORIES[$this->category] ?? $this->category);
    }

    protected function casts(): array
    {
        return [
            'title'        => 'array',
            'caption'      => 'array',
            'taken_at'     => 'date',
            'is_published' => 'boolean',
        ];
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
     * Best-effort thumbnail for grid cards. Falls back through:
     *   image_path → YouTube hqdefault → null.
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

    /**
     * Convert any YouTube URL into an embed URL the iframe can load.
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (! $this->youtube_url) return null;
        if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([\w-]{11})~', $this->youtube_url, $m)) {
            return "https://www.youtube.com/embed/{$m[1]}?rel=0";
        }
        return $this->youtube_url;
    }
}
