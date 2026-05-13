<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['question', 'answer', 'sort_order', 'is_published'])]
class Faq extends Model
{
    use HasJsonTranslations;

    public array $translatable = ['question', 'answer'];

    protected function casts(): array
    {
        return [
            'question'     => 'array',
            'answer'       => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)->orderBy('sort_order');
    }
}
