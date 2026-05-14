<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_number',
        'final_score',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $certificate) {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = static::generateNumber();
            }
            if (empty($certificate->issued_at)) {
                $certificate->issued_at = now();
            }
        });

        static::created(function (self $certificate) {
            if ($certificate->user) {
                $certificate->user->notify(new \App\Notifications\CertificateIssued($certificate));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public static function generateNumber(): string
    {
        do {
            $candidate = 'PJID-'.now()->format('Y').'-'.strtoupper(Str::random(8));
        } while (static::where('certificate_number', $candidate)->exists());

        return $candidate;
    }
}
