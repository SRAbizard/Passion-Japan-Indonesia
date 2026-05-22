<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Gallery now represents an ALBUM (a folder): a title, a description,
 * a cover image, and many GalleryItem rows hanging off it. The actual
 * photos / videos live in gallery_items.
 */
#[Fillable([
    'slug', 'title', 'caption', 'cover_image_path',
    'taken_at', 'sort_order', 'is_published',
])]
class Gallery extends Model
{
    use HasJsonTranslations;

    public array $translatable = ['title', 'caption'];

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

    public function items(): HasMany
    {
        return $this->hasMany(GalleryItem::class)->orderBy('sort_order');
    }

    public function publishedItems(): HasMany
    {
        return $this->items()->where('is_published', true);
    }

    /**
     * URL of the album cover. Falls back to the first published item's
     * thumbnail so an album never renders as a blank card.
     */
    public function getCoverUrlAttribute(): ?string
    {
        if ($this->cover_image_path) {
            return asset('storage/'.$this->cover_image_path);
        }
        $first = $this->publishedItems()->first();
        return $first?->thumbnail_url;
    }
}
