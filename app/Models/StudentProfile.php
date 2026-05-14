<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected function casts(): array
    {
        return [
            'birthdate'           => 'date',
            'passport_expires_at' => 'date',
            'smoker'              => 'boolean',
            'drinker'             => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/'.$this->photo_path) : null;
    }

    /**
     * Compute profile completion percentage based on filled key fields.
     */
    public function completionPct(): int
    {
        $fields = [
            'full_name', 'gender', 'birthdate', 'birthplace', 'religion',
            'id_number', 'address', 'city', 'province',
            'emergency_contact_name', 'emergency_contact_phone',
        ];
        $filled = collect($fields)->filter(fn ($f) => filled($this->$f))->count();
        return (int) round(($filled / count($fields)) * 100);
    }
}
