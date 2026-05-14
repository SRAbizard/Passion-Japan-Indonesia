<?php

namespace App\Models;

use App\Support\HasJsonTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    use HasFactory, HasJsonTranslations;

    protected $fillable = [
        'quiz_id',
        'question',
        'choices',
        'correct_answer',
        'points',
        'sort_order',
    ];

    public array $translatable = ['question'];

    protected function casts(): array
    {
        return [
            'question' => 'array',
            'choices' => 'array',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the choice text for the current locale.
     */
    public function choiceText(string $key, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        foreach ($this->choices ?? [] as $choice) {
            if (($choice['key'] ?? null) === $key) {
                $text = $choice['text'] ?? [];
                if (is_array($text)) {
                    return $text[$locale] ?? $text['id'] ?? $text['en'] ?? (reset($text) ?: '');
                }
                return (string) $text;
            }
        }
        return '';
    }
}
