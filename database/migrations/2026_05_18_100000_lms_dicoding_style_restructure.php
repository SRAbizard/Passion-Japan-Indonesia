<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * LMS restructure — Dicoding/ArkaLearn-style.
 *
 * After this migration:
 *  - A chapter can have MANY quizzes (was hasOne). E.g. "Pt 1" contains
 *    7 separate quiz items LSN401_..LSN407.
 *  - Each quiz/material has an optional `code` field for naming
 *    conventions like "N42601_Kata Kerja".
 *  - Quizzes get a `subtitle` JSON field (translatable) for the small
 *    line under the title on the intro page.
 *  - Quizzes get a `sort_order` so they can be interleaved with
 *    materials in the chapter timeline.
 *  - Materials get `embed_url` so admin can paste a Genially/Canva/
 *    YouTube iframe URL alongside the existing video/pdf/text types.
 *  - The materials.type enum is expanded with 'embed'.
 *  - Chapters get an `unlock_mode` ('free' | 'sequential') for the
 *    Dicoding-style locked progression.
 *
 * The JLPT-section field (`section`) on quiz_questions is left in place
 * for safety, but admin UI stops surfacing it. Same for the passages
 * table — kept in case anyone wants to add reading-passage flows back
 * in later, but no longer wired into the simplified Quiz form.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Materials — embed_url + code, expand type enum
        Schema::table('materials', function (Blueprint $table) {
            $table->string('code', 32)->nullable()->after('id')
                  ->comment('Admin-facing code, e.g. N42601');
            $table->string('embed_url', 500)->nullable()->after('video_url')
                  ->comment('Iframe URL (Genially, Canva, etc.) when type=embed');
        });
        // MySQL: ALTER ENUM in-place
        \DB::statement("ALTER TABLE materials MODIFY COLUMN type ENUM('text','video','embed','pdf') NOT NULL DEFAULT 'text'");

        // 2. Quizzes — code, subtitle, sort_order
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('code', 32)->nullable()->after('id')
                  ->comment('Admin-facing code, e.g. LSN401');
            $table->json('subtitle')->nullable()->after('title')
                  ->comment('Small line below title on quiz intro page');
            $table->unsignedInteger('sort_order')->default(0)->after('max_attempts')
                  ->comment('Position in the chapter timeline (shared key with materials)');
            $table->index(['chapter_id', 'sort_order']);
        });

        // 3. Chapters — unlock_mode
        Schema::table('chapters', function (Blueprint $table) {
            $table->enum('unlock_mode', ['free', 'sequential'])
                  ->default('free')
                  ->after('sort_order')
                  ->comment('free = all items always open; sequential = next item unlocks only after previous one done');
        });
    }

    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn('unlock_mode');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropIndex(['chapter_id', 'sort_order']);
            $table->dropColumn(['code', 'subtitle', 'sort_order']);
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['code', 'embed_url']);
        });
        \DB::statement("ALTER TABLE materials MODIFY COLUMN type ENUM('text','video','pdf') NOT NULL DEFAULT 'text'");
    }
};
