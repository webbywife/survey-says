<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','researcher','interviewer','supervisor') NOT NULL DEFAULT 'researcher'");
    }

    public function down(): void
    {
        // Demote any interviewer/supervisor back to researcher before shrinking enum
        DB::statement("UPDATE users SET role = 'researcher' WHERE role IN ('interviewer','supervisor')");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','researcher') NOT NULL DEFAULT 'researcher'");
    }
};
