<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Fields the Japanese CV (履歴書) needs beyond the existing profile. */
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('name_kana', 160)->nullable()->after('full_name');      // フリガナ (氏名)
            $table->string('nationality', 80)->nullable()->after('name_kana');     // 国籍, default インドネシア
            $table->string('desired_job', 160)->nullable()->after('nationality');  // 応募職種
            $table->string('address_kana', 255)->nullable()->after('address');     // フリガナ (現住所)
            $table->string('line_id', 80)->nullable()->after('postal_code');
            $table->unsignedTinyInteger('japan_visits_study')->default(0)->after('line_id');
            $table->unsignedTinyInteger('japan_visits_work')->default(0)->after('japan_visits_study');
            $table->unsignedTinyInteger('japan_visits_other')->default(0)->after('japan_visits_work');
            $table->json('licenses')->nullable()->after('japan_visits_other');     // [{year, month, name}]
            $table->text('self_pr')->nullable()->after('licenses');                // 自己PR
            $table->text('cv_other')->nullable()->after('self_pr');                // その他 (diet, needs…)
        });
    }

    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'name_kana', 'nationality', 'desired_job', 'address_kana', 'line_id',
                'japan_visits_study', 'japan_visits_work', 'japan_visits_other',
                'licenses', 'self_pr', 'cv_other',
            ]);
        });
    }
};
