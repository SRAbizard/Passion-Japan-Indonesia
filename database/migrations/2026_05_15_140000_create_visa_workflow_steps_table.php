<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visa_workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_category_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('title');                                  // translatable {id,en,ja}
            $table->string('icon_path')->nullable();                // uploaded illustration
            $table->string('icon', 80)->nullable();                 // heroicon fallback
            $table->json('badge_label')->nullable();                // translatable
            $table->enum('badge_color', ['brand', 'warning', 'info', 'success'])->nullable();
            $table->timestamps();

            $table->index(['visa_category_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_workflow_steps');
    }
};
