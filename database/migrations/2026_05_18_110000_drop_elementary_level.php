<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Drop the "elementary" level from courses.level enum. The level
 * taxonomy collapses to: beginner | intermediate | advanced.
 *
 * Any existing course at the elementary level (none at write time)
 * is migrated to "beginner" to satisfy the new enum constraint.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('courses')->where('level', 'elementary')->update(['level' => 'beginner']);
        DB::statement("ALTER TABLE courses MODIFY COLUMN level ENUM('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE courses MODIFY COLUMN level ENUM('beginner','elementary','intermediate','advanced') NOT NULL DEFAULT 'beginner'");
    }
};
