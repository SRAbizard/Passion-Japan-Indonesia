<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['slug', 'post_category_id', 'author_id', 'title', 'excerpt', 'body', 'seo_title', 'seo_description', 'thumbnail_path', 'tags', 'published_at', 'is_featured'])]
class Post extends Model
{
    use HasJsonTranslations;

    public array $translatable = ['title', 'excerpt', 'body', 'seo_title', 'seo_description'];

    protected function casts(): array
    {
        return [
            'title'           => 'array',
            'excerpt'         => 'array',
            'body'            => 'array',
            'seo_title'       => 'array',
            'seo_description' => 'array',
            'tags'            => 'array',
            'published_at'    => 'datetime',
            'is_featured'     => 'boolean',
        ];
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? asset('storage/'.$this->thumbnail_path) : null;
    }
}
