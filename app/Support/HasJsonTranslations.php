<?php

namespace App\Support;

/**
 * Lightweight translatable helper — wire-compatible with Spatie\Translatable
 * (same JSON shape on disk) but works with Filament's dot-notation form binding
 * because the underlying attribute stays a plain array.
 *
 * Usage on a model:
 *   use HasJsonTranslations;
 *   protected function casts(): array { return ['title' => 'array', ...]; }
 *
 * In Blade / controllers: $post->t('title') returns current-locale string,
 * falling back to id → en → first available.
 */
trait HasJsonTranslations
{
    public function t(string $field, ?string $locale = null, mixed $default = null): string
    {
        $locale ??= app()->getLocale();
        $raw = $this->getAttribute($field);

        if (! is_array($raw)) {
            return (string) ($raw ?? $default ?? '');
        }

        return (string) (
            $raw[$locale]
            ?? $raw['id']
            ?? $raw['en']
            ?? reset($raw)
            ?? $default
            ?? ''
        );
    }

    public function setTranslation(string $field, string $locale, mixed $value): static
    {
        $current = (array) ($this->getAttribute($field) ?: []);
        $current[$locale] = $value;
        $this->setAttribute($field, $current);
        return $this;
    }

    public function getTranslations(string $field): array
    {
        $raw = $this->getAttribute($field);
        return is_array($raw) ? $raw : [];
    }
}
