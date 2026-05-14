<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    use HasFactory;

    public const VISA_TARGET_STATUSES = ['pending', 'confirmed', 'rejected', 'changed'];

    public const VISA_TARGET_STATUS_COLORS = [
        'pending'   => 'warning',
        'confirmed' => 'success',
        'rejected'  => 'danger',
        'changed'   => 'info',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected function casts(): array
    {
        return [
            'birthdate'                => 'date',
            'passport_expires_at'      => 'date',
            'smoker'                   => 'boolean',
            'drinker'                  => 'boolean',
            'visa_target_requested_at' => 'datetime',
            'visa_target_reviewed_at'  => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function primaryVisa(): BelongsTo
    {
        return $this->belongsTo(VisaCategory::class, 'primary_visa_category_id');
    }

    public function visaTargetReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'visa_target_reviewed_by');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/'.$this->photo_path) : null;
    }

    /**
     * Has the student's chosen visa been confirmed by an admin?
     */
    public function hasConfirmedVisa(): bool
    {
        return $this->visa_target_status === 'confirmed' && $this->primary_visa_category_id !== null;
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
