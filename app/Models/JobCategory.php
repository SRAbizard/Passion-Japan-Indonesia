<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['slug', 'name', 'icon', 'color', 'sort_order'])]
class JobCategory extends Model
{
    use HasJsonTranslations;

    public array $translatable = ['name'];

    protected function casts(): array
    {
        return ['name' => 'array'];
    }

    public function vacancies(): HasMany
    {
        return $this->hasMany(JobVacancy::class);
    }
}
