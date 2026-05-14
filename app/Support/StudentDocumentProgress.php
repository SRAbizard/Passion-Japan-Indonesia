<?php

namespace App\Support;

use App\Models\User;
use App\Models\VisaCategory;

/**
 * Compute a student's document-completion progress against the required
 * document set defined per visa category.
 *
 * Required = union of required_documents across visa categories from the
 *            student's applications (deduplicated). Falls back to the
 *            "default" set if the student has no applications yet.
 */
class StudentDocumentProgress
{
    /**
     * The default required types when a student has no applications yet.
     * Common baseline that every visa needs.
     */
    public const DEFAULT_REQUIRED = ['ktp', 'passport', 'ijazah', 'photo'];

    /**
     * Visa category slugs the student is targeting (via their applications).
     *
     * @return array<int, string>
     */
    public static function targetVisaSlugs(User $user): array
    {
        return $user->applications()
            ->with('vacancy.visaCategory')
            ->get()
            ->pluck('vacancy.visaCategory.slug')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Required document type keys for this user.
     *
     * @return array<int, string>
     */
    public static function requiredTypes(User $user): array
    {
        $slugs = static::targetVisaSlugs($user);

        if (empty($slugs)) {
            return static::DEFAULT_REQUIRED;
        }

        $types = VisaCategory::whereIn('slug', $slugs)->get()
            ->flatMap(fn ($visa) => $visa->requiredDocumentTypes())
            ->unique()
            ->values()
            ->all();

        return ! empty($types) ? $types : static::DEFAULT_REQUIRED;
    }

    /**
     * Compute progress: required types vs uploaded vs verified.
     *
     * @return array{
     *     required: array<int, string>,
     *     required_count: int,
     *     uploaded_types: array<int, string>,
     *     uploaded_count: int,
     *     verified_types: array<int, string>,
     *     verified_count: int,
     *     missing_types: array<int, string>,
     *     missing_count: int,
     *     pct: int,
     *     using_default: bool,
     * }
     */
    public static function for(User $user): array
    {
        $required = static::requiredTypes($user);
        $usingDefault = empty(static::targetVisaSlugs($user));

        $docs = $user->studentDocuments()->get();

        $uploadedTypes = $docs->whereIn('type', $required)
            ->pluck('type')->unique()->values()->all();

        $verifiedTypes = $docs->where('status', 'verified')
            ->whereIn('type', $required)
            ->pluck('type')->unique()->values()->all();

        $missingTypes = array_values(array_diff($required, $uploadedTypes));

        $pct = count($required) > 0
            ? (int) round((count($verifiedTypes) / count($required)) * 100)
            : 0;

        return [
            'required'        => $required,
            'required_count'  => count($required),
            'uploaded_types'  => $uploadedTypes,
            'uploaded_count'  => count($uploadedTypes),
            'verified_types'  => $verifiedTypes,
            'verified_count'  => count($verifiedTypes),
            'missing_types'   => $missingTypes,
            'missing_count'   => count($missingTypes),
            'pct'             => $pct,
            'using_default'   => $usingDefault,
        ];
    }
}
