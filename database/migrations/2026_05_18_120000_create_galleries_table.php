<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 120)->unique();
            $table->json('title')->nullable();
            $table->json('caption')->nullable();
            $table->enum('type', ['image', 'video', 'youtube'])->default('image');
            $table->string('image_path', 500)->nullable()
                  ->comment('When type=image; also used as poster/thumbnail for video & youtube');
            $table->string('video_path', 500)->nullable()
                  ->comment('When type=video (uploaded mp4)');
            $table->string('youtube_url', 500)->nullable()
                  ->comment('When type=youtube — full watch URL or embed URL');
            $table->date('taken_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
