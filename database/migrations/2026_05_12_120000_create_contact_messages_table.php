<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 32)->nullable();
            $table->string('subject');
            $table->text('message');
            $table->string('locale', 5)->default('id');
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();

            $table->index(['read_at', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
