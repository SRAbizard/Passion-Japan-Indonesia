<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('visa_category_id')->nullable()->constrained()->nullOnDelete();
            $table->json('title');
            $table->json('description');
            $table->json('requirements')->nullable();
            $table->json('benefits')->nullable();
            $table->string('location_city', 80)->nullable();
            $table->string('location_prefecture', 80)->nullable();
            $table->unsignedInteger('salary_min')->nullable();
            $table->unsignedInteger('salary_max')->nullable();
            $table->string('salary_currency', 8)->default('JPY');
            $table->enum('salary_period', ['monthly', 'yearly', 'hourly'])->default('monthly');
            $table->enum('employment_type', ['fulltime', 'parttime', 'contract', 'internship'])->default('fulltime');
            $table->unsignedSmallInteger('positions')->default(1);
            $table->date('expires_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['published_at', 'is_featured']);
            $table->index('visa_category_id');
            $table->index('job_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_vacancies');
    }
};
