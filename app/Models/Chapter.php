<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['course_id', 'title', 'description', 'sort_order', 'is_published'])]
class Chapter extends Model
{
    use HasJsonTranslations;

    public array $translatable = ['title', 'description'];

    protected function casts(): array
    {
        return [
            'title'        => 'array',
            'description'  => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function materials(): HasMany { return $this->hasMany(Material::class)->orderBy('sort_order'); }

    /**
     * The per-chapter quiz (one chapter has one quiz of type=chapter).
     */
    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class)->where('type', 'chapter');
    }
}
