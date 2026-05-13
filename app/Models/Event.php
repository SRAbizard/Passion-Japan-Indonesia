<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['slug', 'event_category_id', 'title', 'description', 'organizer', 'location', 'image_path', 'starts_at', 'ends_at', 'registration_url', 'published_at', 'is_featured'])]
class Event extends Model
{
    use HasJsonTranslations;

    public array $translatable = ['title', 'description', 'organizer', 'location'];

    protected function casts(): array
    {
        return [
            'title'        => 'array',
            'description'  => 'array',
            'organizer'    => 'array',
            'location'     => 'array',
            'starts_at'    => 'datetime',
            'ends_at'      => 'datetime',
            'published_at' => 'datetime',
            'is_featured'  => 'boolean',
        ];
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->where('starts_at', '>=', now());
    }

    public function scopePast(Builder $q): Builder
    {
        return $q->where('starts_at', '<', now());
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }
}
