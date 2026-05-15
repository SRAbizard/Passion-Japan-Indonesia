<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'value', 'group'])]
class Setting extends Model
{
    public static function get(string $key, mixed $default = null): mixed
    {
        $cached = Cache::rememberForever("setting:{$key}", function () use ($key) {
            $row = static::query()->where('key', $key)->first();
            if (! $row) return null;

            // If the stored value looks like JSON, decode it; otherwise return raw.
            $raw = $row->value;
            if (is_string($raw) && (str_starts_with($raw, '[') || str_starts_with($raw, '{'))) {
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return ['v' => $decoded];
                }
            }
            return ['v' => $raw];
        });

        return $cached ? $cached['v'] : $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        // Arrays are JSON-encoded so longText can hold them; get() decodes back.
        $stored = is_array($value)
            ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            : $value;

        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'group' => $group],
        );
        Cache::forget("setting:{$key}");
    }

    protected static function booted(): void
    {
        static::saved(fn ($s) => Cache::forget("setting:{$s->key}"));
        static::deleted(fn ($s) => Cache::forget("setting:{$s->key}"));
    }
}
