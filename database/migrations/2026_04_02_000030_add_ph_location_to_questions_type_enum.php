<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE questions MODIFY COLUMN type ENUM(
            'single_choice',
            'multi_select',
            'open_text',
            'rating',
            'number',
            'date',
            'time',
            'grid',
            'ph_location'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE questions MODIFY COLUMN type ENUM(
            'single_choice',
            'multi_select',
            'open_text',
            'rating',
            'number',
            'date',
            'time',
            'grid'
        ) NOT NULL");
    }
};
