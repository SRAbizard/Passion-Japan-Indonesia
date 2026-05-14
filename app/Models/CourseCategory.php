<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['slug', 'name', 'description', 'color', 'icon', 'sort_order'])]
class CourseCategory extends Model
{
    use HasJsonTranslations;

    public array $translatable = ['name', 'description'];

    protected function casts(): array
    {
        return [
            'name'        => 'array',
            'description' => 'array',
        ];
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
