<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The Languages tab became the Licences & certifications tab, which
     * keeps its picks in student_profiles.licenses. Copy the language rows
     * that name a known test (JLPT, JFT, TOEFL, IELTS) over; the
     * student_languages rows themselves are left as they are.
     */
    public function up(): void
    {
        $rows = DB::table('student_languages')->whereNotNull('proficiency')->get()->groupBy('user_id');

        foreach ($rows as $userId => $languages) {
            $profile = DB::table('student_profiles')->where('user_id', $userId)->first();
            if (! $profile) {
                continue;
            }
            $licenses = json_decode($profile->licenses ?? '[]', true) ?: [];
            $types    = array_column($licenses, 'type');

            foreach ($languages as $language) {
                $entry = $this->entry((string) $language->proficiency);
                if (! $entry || in_array($entry['type'], $types, true)) {
                    continue;
                }
                if ($language->taken_at) {
                    $date = Carbon::parse($language->taken_at);
                    $entry += ['year' => $date->year, 'month' => $date->month];
                }
                $licenses[] = $entry;
                $types[]    = $entry['type'];
            }

            DB::table('student_profiles')->where('id', $profile->id)
                ->update(['licenses' => json_encode($licenses, JSON_UNESCAPED_UNICODE)]);
        }
    }

    /** "JLPT N3" → jlpt_n3, "JFT A2" → jft_a2, "IELTS 6.5" → ielts with its score. */
    private function entry(string $proficiency): ?array
    {
        return match (true) {
            (bool) preg_match('/JLPT\s*N([1-4])/i', $proficiency, $m) => ['group' => 'language', 'type' => 'jlpt_n'.$m[1]],
            (bool) preg_match('/JFT/i', $proficiency)                 => ['group' => 'language', 'type' => 'jft_a2'],
            (bool) preg_match('/(TOEFL|IELTS)\D*([\d.]+)?/i', $proficiency, $m) => array_filter([
                'group' => 'other', 'type' => strtolower($m[1]), 'score' => $m[2] ?? null,
            ]),
            default => null,
        };
    }

    public function down(): void
    {
        // The copied entries are ordinary licence picks; nothing to undo.
    }
};
