<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('course_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('title');
            $table->json('subtitle')->nullable();
            $table->json('description');
            $table->json('what_you_learn')->nullable();
            $table->json('prerequisites')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('intro_video_url')->nullable();
            $table->enum('level', ['beginner', 'elementary', 'intermediate', 'advanced'])->default('beginner');
            $table->unsignedInteger('duration_days')->default(365);
            $table->unsignedInteger('price')->default(0);
            $table->string('currency', 8)->default('IDR');
            $table->boolean('is_free')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['published_at', 'is_featured']);
            $table->index('course_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
