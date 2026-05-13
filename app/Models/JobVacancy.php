<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug', 'company_id', 'job_category_id', 'visa_category_id',
    'title', 'description', 'requirements', 'benefits',
    'location_city', 'location_prefecture',
    'salary_min', 'salary_max', 'salary_currency', 'salary_period',
    'employment_type', 'positions',
    'expires_at', 'published_at', 'is_featured',
])]
class JobVacancy extends Model
{
    use HasJsonTranslations;

    public array $translatable = ['title', 'description', 'requirements', 'benefits'];

    protected function casts(): array
    {
        return [
            'title'        => 'array',
            'description'  => 'array',
            'requirements' => 'array',
            'benefits'     => 'array',
            'expires_at'   => 'date',
            'published_at' => 'datetime',
            'is_featured'  => 'boolean',
        ];
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()->toDateString()));
    }

    public function scopeFeatured(Builder $q): Builder
    {
        return $q->where('is_featured', true);
    }

    public function company(): BelongsTo            { return $this->belongsTo(Company::class); }
    public function jobCategory(): BelongsTo        { return $this->belongsTo(JobCategory::class); }
    public function visaCategory(): BelongsTo       { return $this->belongsTo(VisaCategory::class); }
    public function applications(): HasMany         { return $this->hasMany(Application::class); }

    public function getSalaryRangeAttribute(): ?string
    {
        if (! $this->salary_min && ! $this->salary_max) return null;
        $fmt = fn ($n) => $this->salary_currency === 'JPY' ? '¥'.number_format($n / 1000).'K' : number_format($n);

        if ($this->salary_min && $this->salary_max) {
            return $fmt($this->salary_min).'–'.$fmt($this->salary_max);
        }
        return $fmt($this->salary_min ?: $this->salary_max);
    }

    public function getLocationDisplayAttribute(): string
    {
        return collect([$this->location_city, $this->location_prefecture])->filter()->join(', ') ?: '—';
    }
}
