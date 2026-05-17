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
        'section',
        'passage_id',
        'question',
        'choices',
        'image_path',
        'audio_path',
        'max_audio_plays',
        'correct_answer',
        'points',
        'sort_order',
    ];

    public array $translatable = ['question'];

    protected function casts(): array
    {
        return [
            'question'        => 'array',
            'choices'         => 'array',
            'max_audio_plays' => 'integer',
        ];
    }

    /**
     * Map for displaying JLPT section labels in the UI.
     */
    public const SECTIONS = [
        'choukai' => '聴解 (Choukai / Listening)',
        'dokkai'  => '読解 (Dokkai / Reading)',
        'bunpou'  => '文法 (Bunpou / Grammar)',
        'kotoba'  => '言葉 (Kotoba / Vocabulary)',
        'kanji'   => '漢字 (Kanji)',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function passage(): BelongsTo
    {
        return $this->belongsTo(Passage::class);
    }

    /**
     * Public URL for the question image, or null.
     */
    public function imageUrl(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }

    /**
     * Public URL for the Choukai audio file, or null.
     */
    public function audioUrl(): ?string
    {
        return $this->audio_path ? asset('storage/'.$this->audio_path) : null;
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
