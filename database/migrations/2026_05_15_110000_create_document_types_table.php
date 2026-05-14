<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('key', 64)->unique();           // e.g. ktp, paspor — used by student_documents.type
            $table->json('label');                         // {"id":"KTP","en":"ID Card","ja":"身分証"}
            $table->json('description')->nullable();
            $table->string('icon', 80)->default('heroicon-o-document');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
