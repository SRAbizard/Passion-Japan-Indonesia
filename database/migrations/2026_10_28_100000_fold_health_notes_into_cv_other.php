<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Allergies and medical conditions left the profile form; students now
     * write them in "Other notes" (cv_other). Append what was entered there,
     * labelled the way the CV used to print it. The old columns are kept.
     */
    public function up(): void
    {
        DB::table('student_profiles')
            ->where(fn ($q) => $q->whereNotNull('allergies')->where('allergies', '!=', '')
                ->orWhere(fn ($q) => $q->whereNotNull('medical_conditions')->where('medical_conditions', '!=', '')))
            ->get()
            ->each(function ($p) {
                $notes = trim((string) $p->cv_other);
                foreach (['allergies' => 'アレルギー：', 'medical_conditions' => '持病：'] as $column => $label) {
                    $value = trim((string) $p->{$column});
                    if ($value !== '' && ! str_contains($notes, $value)) {
                        $notes = trim($notes."\n".$label.$value);
                    }
                }
                DB::table('student_profiles')->where('id', $p->id)->update(['cv_other' => $notes]);
            });
    }

    public function down(): void
    {
        // The notes are the student's own text from here on; nothing to undo.
    }
};
