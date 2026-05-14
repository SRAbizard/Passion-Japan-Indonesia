<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Biodata
            $table->string('full_name')->nullable();
            $table->string('nickname', 80)->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('birthdate')->nullable();
            $table->string('birthplace', 120)->nullable();
            $table->string('religion', 40)->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();

            // ID & Passport
            $table->string('id_number', 32)->nullable();
            $table->string('passport_number', 32)->nullable();
            $table->date('passport_expires_at')->nullable();

            // Address
            $table->string('address', 255)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('province', 80)->nullable();
            $table->string('postal_code', 16)->nullable();

            // Emergency contact
            $table->string('emergency_contact_name', 120)->nullable();
            $table->string('emergency_contact_relation', 40)->nullable();
            $table->string('emergency_contact_phone', 32)->nullable();

            // Health
            $table->unsignedSmallInteger('height_cm')->nullable();
            $table->unsignedSmallInteger('weight_kg')->nullable();
            $table->string('blood_type', 8)->nullable();
            $table->text('allergies')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->boolean('smoker')->default(false);
            $table->boolean('drinker')->default(false);

            // Photo
            $table->string('photo_path')->nullable();

            $table->timestamps();
        });

        Schema::create('student_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('level', ['sd', 'smp', 'sma', 'smk', 'd1', 'd3', 's1', 's2', 's3', 'other'])->default('sma');
            $table->string('institution', 160);
            $table->string('major', 120)->nullable();
            $table->unsignedSmallInteger('start_year')->nullable();
            $table->unsignedSmallInteger('end_year')->nullable();
            $table->string('gpa', 16)->nullable();
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        Schema::create('student_work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('company', 160);
            $table->string('position', 120);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('student_family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('relation', ['father', 'mother', 'guardian', 'sibling', 'spouse', 'child', 'other'])->default('father');
            $table->string('name', 160);
            $table->string('occupation', 120)->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('address', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('student_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('language', 80);                    // e.g. Japanese, English
            $table->string('proficiency', 80)->nullable();     // e.g. JLPT N3, IELTS 6.5
            $table->string('certificate_number', 80)->nullable();
            $table->date('taken_at')->nullable();
            $table->string('certificate_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_languages');
        Schema::dropIfExists('student_family_members');
        Schema::dropIfExists('student_work_experiences');
        Schema::dropIfExists('student_education');
        Schema::dropIfExists('student_profiles');
    }
};
