<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add study_leader to the role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','researcher','interviewer','supervisor','study_leader') NOT NULL DEFAULT 'researcher'");

        // Add check/approve audit columns to responses
        Schema::table('responses', function (Blueprint $table) {
            $table->timestamp('checked_at')->nullable()->after('is_offline_sync');
            $table->foreignId('checked_by')->nullable()->after('checked_at')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('checked_by');
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropForeign(['checked_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['checked_at', 'checked_by', 'approved_at', 'approved_by']);
        });

        DB::statement("UPDATE users SET role = 'researcher' WHERE role = 'study_leader'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','researcher','interviewer','supervisor') NOT NULL DEFAULT 'researcher'");
    }
};
