<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reading passages (Dokkai). One passage can be shared by multiple
 * questions inside the same quiz — typical JLPT pattern: a paragraph
 * of Japanese text followed by 3–5 questions about it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->json('title')->nullable();          // optional short label
            $table->json('content');                    // the Japanese text
            $table->json('translation')->nullable();    // optional translation (e.g. ID)
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passages');
    }
};
