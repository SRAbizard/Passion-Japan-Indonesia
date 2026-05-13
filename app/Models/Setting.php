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
            return $row ? ['v' => $row->value] : null;
        });

        return $cached ? $cached['v'] : $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group],
        );
        Cache::forget("setting:{$key}");
    }

    protected static function booted(): void
    {
        static::saved(fn ($s) => Cache::forget("setting:{$s->key}"));
        static::deleted(fn ($s) => Cache::forget("setting:{$s->key}"));
    }
}
