<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('visa_categories', function (Blueprint $table) {
            // List of StudentDocument::TYPES keys required for this visa.
            // Stored as JSON array, e.g. ["ktp","kk","passport","ijazah"].
            $table->json('required_documents')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('visa_categories', function (Blueprint $table) {
            $table->dropColumn('required_documents');
        });
    }
};
