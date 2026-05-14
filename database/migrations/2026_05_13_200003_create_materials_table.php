<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();
            $table->json('title');
            $table->enum('type', ['video', 'pdf', 'text'])->default('text');
            $table->string('video_url')->nullable();
            $table->string('pdf_path')->nullable();
            $table->json('content')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_free_preview')->default(false);
            $table->timestamps();

            $table->index(['chapter_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
