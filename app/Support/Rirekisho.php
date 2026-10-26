<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Maps a student's profile onto the Passion Japan 履歴書 (Japanese CV)
 * template: basic information, education & work history, licences,
 * self-PR and other notes. Rendered by RirekishoPdf.
 */
class Rirekisho
{
    /** Minimum rows per table, matching the printed template. */
    public const ROWS = ['education' => 4, 'work' => 10, 'licenses' => 4];

    private const RELIGIONS = [
        'islam'     => 'イスラム教',
        'muslim'    => 'イスラム教',
        'kristen'   => 'キリスト教（プロテスタント）',
        'protestan' => 'キリスト教（プロテスタント）',
        'christian' => 'キリスト教',
        'katolik'   => 'キリスト教（カトリック）',
        'catholic'  => 'キリスト教（カトリック）',
        'hindu'     => 'ヒンドゥー教',
        'buddha'    => '仏教',
        'buddhist'  => '仏教',
        'konghucu'  => '儒教',
    ];

    public static function data(User $user, ?string $desiredJob = null): array
    {
        $user->loadMissing('studentProfile', 'educations', 'workExperiences', 'languages');
        $p = $user->studentProfile;

        $birth = $p?->birthdate;
        $japanese = $user->languages->first(fn ($l) => Str::contains(Str::lower($l->language), ['japan', 'jepang', '日本']));
        $others   = $user->languages->reject(fn ($l) => $l === $japanese)
            ->map(fn ($l) => trim($l->language.($l->proficiency ? " ({$l->proficiency})" : '')))->implode('、');

        return [
            'today'        => now(),
            'name_kana'    => $p?->name_kana,
            'name'         => Str::upper($p?->full_name ?: $user->name),
            'gender'       => match ($p?->gender) { 'male' => '男', 'female' => '女', default => null },
            'nationality'  => $p?->nationality ?: 'インドネシア',
            'birth'        => $birth,
            'age'          => $birth?->age,
            'desired_job'  => $desiredJob ?: $p?->desired_job,
            'address_kana' => $p?->address_kana,
            'postal'       => $p?->postal_code,
            'address'      => collect([$p?->address, $p?->city, $p?->province])->filter()->implode(', '),
            'phone'        => $user->phone,
            'email'        => $user->email,
            'line'         => $p?->line_id,
            'visits'       => [(int) $p?->japan_visits_study, (int) $p?->japan_visits_work, (int) $p?->japan_visits_other],
            'japanese'     => $japanese ? trim(($japanese->proficiency ?: $japanese->language)) : null,
            'other_lang'   => $others ?: null,
            'healthy'      => blank($p?->medical_conditions),
            'health_note'  => $p?->medical_conditions,
            'height'       => $p?->height_cm,
            'weight'       => $p?->weight_kg,
            'religion'     => self::religion($p?->religion),
            'blood'        => $p?->blood_type,
            'photo'        => $p?->photo_path && is_file(storage_path('app/public/'.$p->photo_path)) ? storage_path('app/public/'.$p->photo_path) : null,
            'education'    => self::pad(self::education($user), self::ROWS['education']),
            'work'         => self::pad(self::work($user), self::ROWS['work']),
            'licenses'     => self::pad(self::licenses($user), self::ROWS['licenses']),
            'self_pr'      => $p?->self_pr,
            'other'        => collect([
                $p?->cv_other,
                $p?->allergies ? 'アレルギー：'.$p->allergies : null,
            ])->filter()->implode("\n"),
        ];
    }

    /** Oldest first, as a 履歴書 lists them. */
    private static function education(User $user): array
    {
        return $user->educations->sortBy(fn ($e) => $e->start_year ?? $e->end_year ?? 0)->map(fn ($e) => [
            'from_y' => $e->start_year, 'from_m' => null,
            'to_y'   => $e->is_current ? '在学中' : $e->end_year, 'to_m' => null,
            'text'   => trim($e->institution.($e->major ? '　'.$e->major : '')),
        ])->values()->all();
    }

    private static function work(User $user): array
    {
        return $user->workExperiences->sortBy(fn ($w) => $w->start_date?->timestamp ?? 0)->map(fn ($w) => [
            'from_y' => $w->start_date?->year, 'from_m' => $w->start_date?->month,
            'to_y'   => $w->is_current ? '在職中' : $w->end_date?->year, 'to_m' => $w->is_current ? null : $w->end_date?->month,
            'text'   => trim($w->company.($w->position ? '　'.$w->position : '')),
        ])->values()->all();
    }

    /** Language certificates with a date, plus the licences entered on the profile. */
    private static function licenses(User $user): array
    {
        $fromLanguages = $user->languages->filter(fn ($l) => $l->proficiency && $l->taken_at)->map(fn ($l) => [
            'y' => $l->taken_at->year, 'm' => $l->taken_at->month, 'text' => trim($l->language.' '.$l->proficiency),
        ]);
        $entered = collect($user->studentProfile?->licenses ?? [])->filter(fn ($l) => filled($l['name'] ?? null))->map(fn ($l) => [
            'y' => $l['year'] ?? null, 'm' => $l['month'] ?? null, 'text' => $l['name'],
        ]);

        return $fromLanguages->concat($entered)->sortBy(fn ($l) => sprintf('%04d%02d', (int) $l['y'], (int) $l['m']))->values()->all();
    }

    private static function religion(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }
        foreach (self::RELIGIONS as $needle => $ja) {
            if (Str::contains(Str::lower($value), $needle)) {
                return $ja;
            }
        }

        return $value;
    }

    /** Fill a table up to the template's row count with blank rows. */
    private static function pad(array $rows, int $min): array
    {
        return array_pad($rows, max($min, count($rows)), null);
    }

    public static function filename(User $user): string
    {
        $name = $user->studentProfile?->full_name ?: $user->name;

        return 'PASSION JAPAN INDONESIA_履歴書_'.Str::upper($name).'.pdf';
    }

    /** "2026 年 10 月 26 日 現在" style date parts. */
    public static function dateParts(?Carbon $date): array
    {
        return $date ? [$date->year, $date->month, $date->day] : [null, null, null];
    }
}
