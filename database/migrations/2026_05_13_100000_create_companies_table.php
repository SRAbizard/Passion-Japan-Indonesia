<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('logo_path')->nullable();
            $table->string('website')->nullable();
            $table->string('industry', 80)->nullable();
            $table->string('country', 80)->default('Japan');
            $table->string('city', 80)->nullable();
            $table->json('description')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['country', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
