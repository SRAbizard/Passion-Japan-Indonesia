<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * JLPT-style sectioning + media support for quiz questions:
 *
 *  - section: which JLPT skill area this question belongs to
 *      • choukai   (聴解 / listening)  → audio_path required
 *      • dokkai    (読解 / reading)    → usually attached to a passage
 *      • bunpou    (文法 / grammar)
 *      • kotoba    (言葉 / vocabulary) → often paired with image
 *      • kanji     (漢字)              → often paired with image
 *
 *  - passage_id: link Dokkai questions to a shared reading passage
 *  - image_path: optional illustration (Kotoba / Kanji often need pictures)
 *  - audio_path: Choukai audio file (mp3 / m4a / wav)
 *  - max_audio_plays: replay cap for Choukai (default 2, JLPT-like)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->enum('section', ['choukai', 'dokkai', 'bunpou', 'kotoba', 'kanji'])
                  ->nullable()->after('quiz_id')->index();
            $table->foreignId('passage_id')->nullable()->after('section')
                  ->constrained()->nullOnDelete();
            $table->string('image_path')->nullable()->after('choices');
            $table->string('audio_path')->nullable()->after('image_path');
            $table->unsignedTinyInteger('max_audio_plays')->default(2)->after('audio_path');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropForeign(['passage_id']);
            $table->dropColumn(['section', 'passage_id', 'image_path', 'audio_path', 'max_audio_plays']);
        });
    }
};
