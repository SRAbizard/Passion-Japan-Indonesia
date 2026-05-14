<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['slug', 'name', 'description', 'color', 'sort_order', 'required_documents'])]
class VisaCategory extends Model
{
    use HasJsonTranslations;

    public array $translatable = ['name', 'description'];

    protected function casts(): array
    {
        return [
            'name'               => 'array',
            'description'        => 'array',
            'required_documents' => 'array',
        ];
    }

    public function vacancies(): HasMany
    {
        return $this->hasMany(JobVacancy::class);
    }

    /**
     * Required document type keys (subset of StudentDocument::TYPES).
     */
    public function requiredDocumentTypes(): array
    {
        return array_values(array_filter((array) ($this->required_documents ?? [])));
    }
}
