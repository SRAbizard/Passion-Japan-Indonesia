<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'material_id', 'completed_at'])]
class LessonProgress extends Model
{
    protected $table = 'lesson_progress';

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function material(): BelongsTo { return $this->belongsTo(Material::class); }
}
