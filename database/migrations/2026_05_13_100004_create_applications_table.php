<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_vacancy_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'submitted',
                'under_review',
                'interview_scheduled',
                'offered',
                'accepted',
                'rejected',
                'withdrawn',
            ])->default('submitted');
            $table->text('cover_letter')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'job_vacancy_id'], 'applications_user_job_unique');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
