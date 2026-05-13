<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('post_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('title');         // translatable
            $table->json('excerpt');       // translatable
            $table->json('body');          // translatable (markdown/HTML)
            $table->json('seo_title')->nullable();        // translatable
            $table->json('seo_description')->nullable();  // translatable
            $table->string('thumbnail_path')->nullable();
            $table->json('tags')->nullable();             // ["jepang", "jlpt"] — single shared list, not per-locale
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['published_at', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
