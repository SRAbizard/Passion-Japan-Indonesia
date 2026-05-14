<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('visa_categories', function (Blueprint $table) {
            // Optional document type keys for this visa — accepted if uploaded
            // but not blocking the student's progress percentage. Required +
            // Optional sets must be disjoint (matrix UI enforces this).
            $table->json('optional_documents')->nullable()->after('required_documents');
        });
    }

    public function down(): void
    {
        Schema::table('visa_categories', function (Blueprint $table) {
            $table->dropColumn('optional_documents');
        });
    }
};
