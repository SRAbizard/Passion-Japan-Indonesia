<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaWorkflowStep extends Model
{
    use HasFactory, HasJsonTranslations;

    public const BADGE_COLORS = ['brand', 'warning', 'info', 'success'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public array $translatable = ['title', 'badge_label'];

    protected function casts(): array
    {
        return [
            'title'       => 'array',
            'badge_label' => 'array',
        ];
    }

    public function visa(): BelongsTo
    {
        return $this->belongsTo(VisaCategory::class, 'visa_category_id');
    }

    public function getIconUrlAttribute(): ?string
    {
        return $this->icon_path ? asset('storage/'.$this->icon_path) : null;
    }
}
