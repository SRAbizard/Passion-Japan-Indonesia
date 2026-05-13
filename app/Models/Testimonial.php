<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'role', 'quote', 'avatar_path', 'kind', 'sort_order', 'is_published'])]
class Testimonial extends Model
{
    use HasJsonTranslations;

    public array $translatable = ['role', 'quote'];

    protected function casts(): array
    {
        return [
            'role'         => 'array',
            'quote'        => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)->orderBy('sort_order');
    }

    public function scopeStudents(Builder $q): Builder
    {
        return $q->where('kind', 'student');
    }

    public function scopeCompanies(Builder $q): Builder
    {
        return $q->where('kind', 'company');
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar_path ? asset('storage/'.$this->avatar_path) : null;
    }
}
