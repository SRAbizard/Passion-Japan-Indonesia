<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A quiz now belongs either to a chapter (per-bab quiz, e.g. each of
 * the 25 Mina no Nihongo chapters) or to a course as its final exam.
 *
 *  - type=chapter  → chapter_id required, course_id auto-set from chapter
 *  - type=final    → chapter_id null,    course_id required
 *
 * course_id stays on the table even for chapter quizzes (denormalised)
 * so "all quizzes for course X" queries don't need a chapter join.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('chapter_id')->nullable()->after('course_id')
                  ->constrained()->cascadeOnDelete();
            $table->enum('type', ['chapter', 'final'])->default('chapter')->after('chapter_id');
            $table->index(['course_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['chapter_id']);
            $table->dropIndex(['course_id', 'type']);
            $table->dropColumn(['chapter_id', 'type']);
        });
    }
};
