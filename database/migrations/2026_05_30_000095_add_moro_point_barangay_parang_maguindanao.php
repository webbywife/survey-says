<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('psgc_barangays')->insertOrIgnore([
            'code'      => 'MGN-PARANG-MORO_POINT',
            'name'      => 'Moro Point',
            'city_code' => 'MGN-PARANG',
        ]);
    }

    public function down(): void
    {
        DB::table('psgc_barangays')
            ->where('code', 'MGN-PARANG-MORO_POINT')
            ->delete();
    }
};
