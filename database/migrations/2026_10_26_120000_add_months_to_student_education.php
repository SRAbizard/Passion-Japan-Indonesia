<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** The 履歴書 lists education with year and month (入学 / 卒業). */
    public function up(): void
    {
        Schema::table('student_education', function (Blueprint $table) {
            $table->unsignedTinyInteger('start_month')->nullable()->after('start_year');
            $table->unsignedTinyInteger('end_month')->nullable()->after('end_year');
        });
    }

    public function down(): void
    {
        Schema::table('student_education', function (Blueprint $table) {
            $table->dropColumn(['start_month', 'end_month']);
        });
    }
};
