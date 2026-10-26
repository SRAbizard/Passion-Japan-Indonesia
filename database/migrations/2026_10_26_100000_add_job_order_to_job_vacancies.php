<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_vacancies', function (Blueprint $table) {
            // Structured Job Order (App\Support\JobOrder) used to generate
            // the "Terjemahan Job Order" PDF.
            $table->json('job_order')->nullable()->after('detail_pdf_path');
            $table->string('job_order_code', 40)->nullable()->after('job_order');
            $table->boolean('job_order_public')->default(false)->after('job_order_code');
        });
    }

    public function down(): void
    {
        Schema::table('job_vacancies', function (Blueprint $table) {
            $table->dropColumn(['job_order', 'job_order_code', 'job_order_public']);
        });
    }
};
