<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->foreignId('primary_visa_category_id')
                ->nullable()
                ->after('user_id')
                ->constrained('visa_categories')
                ->nullOnDelete();

            // pending = student requested, awaiting admin
            // confirmed = admin approved this is the student's target
            // rejected = admin said no (with notes)
            // changed = student wants to switch to a different visa, awaiting re-approval
            $table->enum('visa_target_status', ['pending', 'confirmed', 'rejected', 'changed'])
                ->nullable()
                ->after('primary_visa_category_id');

            $table->timestamp('visa_target_requested_at')->nullable()->after('visa_target_status');
            $table->timestamp('visa_target_reviewed_at')->nullable()->after('visa_target_requested_at');
            $table->foreignId('visa_target_reviewed_by')
                ->nullable()
                ->after('visa_target_reviewed_at')
                ->constrained('users')
                ->nullOnDelete();
            $table->text('visa_target_notes')->nullable()->after('visa_target_reviewed_by');

            $table->index(['visa_target_status', 'primary_visa_category_id'], 'sp_visa_target_idx');
        });
    }

    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropIndex('sp_visa_target_idx');
            $table->dropConstrainedForeignId('visa_target_reviewed_by');
            $table->dropConstrainedForeignId('primary_visa_category_id');
            $table->dropColumn([
                'visa_target_status',
                'visa_target_requested_at',
                'visa_target_reviewed_at',
                'visa_target_notes',
            ]);
        });
    }
};
