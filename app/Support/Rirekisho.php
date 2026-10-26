<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Maps a student's profile onto the Passion Japan 履歴書 (Japanese CV):
 * basic information, education & work history, licences, self-PR and
 * other notes. Values follow the agency's filled-in example (e.g.
 * "School　(Major – S1)", "-" for missing contact details) and the
 * dropdown options of the Word template. Used by RirekishoDocx (the
 * Word file) and RirekishoPdf (the PDF preview).
 */
class Rirekisho
{
    /** Rows per table in the template; extra entries add rows. */
    public const ROWS = ['education' => 4, 'work' => 10, 'licenses' => 4];

    /** Profile religion → the template's 宗教 dropdown values. */
    private const RELIGIONS = [
        'islam'     => 'イスラム教',
        'muslim'    => 'イスラム教',
        'katolik'   => 'カトリック',
        'catholic'  => 'カトリック',
        'kristen'   => 'キリスト教',
        'protestan' => 'キリスト教',
        'christian' => 'キリスト教',
        'hindu'     => 'ヒンドゥー教',
        'buddha'    => '仏教',
        'buddhist'  => '仏教',
        'konghucu'  => '儒教',
    ];

    public static function data(User $user, ?string $desiredJob = null): array
    {
        $user->loadMissing('studentProfile', 'educations', 'workExperiences', 'languages');
        $p = $user->studentProfile;

        $birth    = $p?->birthdate;
        $japanese = $user->languages->first(fn ($l) => self::isJapanese($l->language));
        $others   = $user->languages->reject(fn ($l) => $l === $japanese)
            ->map(fn ($l) => trim($l->language.($l->proficiency ? " ({$l->proficiency})" : '')))->implode('、');
        $photo    = $p?->photo_path ? storage_path('app/public/'.$p->photo_path) : null;

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
            'address'      => Str::upper(trim(($p?->postal_code ? '〒'.$p->postal_code.' ' : '').collect([$p?->address, $p?->city, $p?->province])->filter()->implode(', '))),
            'phone'        => $user->phone ?: '-',
            'email'        => $user->email ?: '-',
            'line'         => $p?->line_id ?: '-',
            'visits'       => [(int) $p?->japan_visits_study, (int) $p?->japan_visits_work, (int) $p?->japan_visits_other],
            'japanese'     => $japanese ? trim($japanese->proficiency ?: $japanese->language) : null,
            'other_lang'   => $others ?: '-',
            'healthy'      => blank($p?->medical_conditions),
            'health_note'  => $p?->medical_conditions,
            'height'       => $p?->height_cm,
            'weight'       => $p?->weight_kg,
            'religion'     => self::religion($p?->religion),
            'blood'        => in_array($p?->blood_type, ['A', 'B', 'O', 'AB'], true) ? $p->blood_type : null,
            'photo'        => $photo && is_file($photo) ? $photo : null,
            'education'    => self::education($user),
            'work'         => self::work($user),
            'licenses'     => self::licenses($user),
            'self_pr'      => $p?->self_pr,
            'other'        => collect([
                $p?->cv_other,
                $p?->allergies ? 'アレルギー：'.$p->allergies : null,
            ])->filter()->implode("\n"),
        ];
    }

    /** Oldest first, as a 履歴書 lists them: "School　(Major – S1)". */
    private static function education(User $user): array
    {
        return $user->educations
            ->sortBy(fn ($e) => sprintf('%04d%02d', $e->start_year ?? $e->end_year ?? 0, $e->start_month ?? 0))
            ->map(function ($e) {
                $level  = $e->level && $e->level !== 'other' ? Str::upper($e->level) : null;
                $detail = collect([$e->major, $level])->filter()->implode(' – ');

                return [
                    'from_y' => $e->start_year, 'from_m' => $e->start_month,
                    'to_y'   => $e->is_current ? '在学中' : $e->end_year, 'to_m' => $e->is_current ? null : $e->end_month,
                    'text'   => $e->institution.($detail !== '' ? '　('.$detail.')' : ''),
                ];
            })->values()->all();
    }

    /** "Company　(Position)", oldest first. */
    private static function work(User $user): array
    {
        return $user->workExperiences->sortBy(fn ($w) => $w->start_date?->timestamp ?? 0)->map(fn ($w) => [
            'from_y' => $w->start_date?->year, 'from_m' => $w->start_date?->month,
            'to_y'   => $w->is_current ? '在職中' : $w->end_date?->year, 'to_m' => $w->is_current ? null : $w->end_date?->month,
            'text'   => $w->company.($w->position ? '　('.$w->position.')' : ''),
        ])->values()->all();
    }

    /** Dated language certificates (named the Japanese way) plus the licences entered on the profile. */
    private static function licenses(User $user): array
    {
        $fromLanguages = $user->languages->filter(fn ($l) => $l->proficiency && $l->taken_at)->map(fn ($l) => [
            'y' => $l->taken_at->year, 'm' => $l->taken_at->month, 'text' => self::certificateName($l->language, $l->proficiency),
        ]);
        $entered = collect($user->studentProfile?->licenses ?? [])->filter(fn ($l) => filled($l['name'] ?? null))->map(fn ($l) => [
            'y' => $l['year'] ?? null, 'm' => $l['month'] ?? null, 'text' => $l['name'],
        ]);

        return $fromLanguages->concat($entered)->sortBy(fn ($l) => sprintf('%04d%02d', (int) $l['y'], (int) $l['m']))->values()->all();
    }

    /** "JLPT N4" → "日本語能力試験N4　合格", "JFT A2" → "国際交流基金日本語基礎テストA2　合格". */
    private static function certificateName(string $language, string $proficiency): string
    {
        if (preg_match('/JLPT\s*(N[1-5])/i', $proficiency, $m)) {
            return '日本語能力試験'.Str::upper($m[1]).'　合格';
        }
        if (preg_match('/JFT[^A-Z0-9]*(A1|A2)?/i', $proficiency, $m)) {
            return '国際交流基金日本語基礎テスト'.Str::upper($m[1] ?? '').'　合格';
        }

        return trim($language.' '.$proficiency);
    }

    private static function isJapanese(?string $language): bool
    {
        return Str::contains(Str::lower((string) $language), ['japan', 'jepang', '日本']);
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

        return null; // not one of the template's options
    }

    public static function filename(User $user, string $ext = 'pdf'): string
    {
        $name = $user->studentProfile?->full_name ?: $user->name;

        return 'PASSION JAPAN INDONESIA_履歴書_'.Str::upper($name).'.'.$ext;
    }

    /** [year, month, day] for the "年 月 日 現在" line. */
    public static function dateParts(?Carbon $date): array
    {
        return $date ? [$date->year, $date->month, $date->day] : [null, null, null];
    }
}
