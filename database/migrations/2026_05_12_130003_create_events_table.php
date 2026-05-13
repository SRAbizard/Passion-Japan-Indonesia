<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('event_category_id')->nullable()->constrained()->nullOnDelete();
            $table->json('title');
            $table->json('description');
            $table->json('organizer');
            $table->json('location');
            $table->string('image_path')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->string('registration_url')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['starts_at', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
