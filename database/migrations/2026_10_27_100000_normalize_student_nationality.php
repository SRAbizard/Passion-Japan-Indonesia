<?php

use App\Support\Rirekisho;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Nationality became a dropdown stored in English ("Indonesia"); turn
     * values typed in Japanese or another case ("インドネシア", "indonesia")
     * into the dropdown keys.
     */
    public function up(): void
    {
        foreach (Rirekisho::NATIONALITIES as $en => $ja) {
            DB::table('student_profiles')
                ->where(fn ($q) => $q->where('nationality', $ja)->orWhereRaw('LOWER(nationality) = ?', [strtolower($en)]))
                ->update(['nationality' => $en]);
        }
    }

    public function down(): void
    {
        // Keys are a valid nationality value; nothing to undo.
    }
};
