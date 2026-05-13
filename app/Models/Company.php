<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['slug', 'name', 'logo_path', 'website', 'industry', 'country', 'city', 'description', 'is_verified', 'is_active'])]
class Company extends Model
{
    use HasJsonTranslations;

    public array $translatable = ['description'];

    protected function casts(): array
    {
        return [
            'description' => 'array',
            'is_verified' => 'boolean',
            'is_active'   => 'boolean',
        ];
    }

    public function vacancies(): HasMany
    {
        return $this->hasMany(JobVacancy::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/'.$this->logo_path) : null;
    }
}
