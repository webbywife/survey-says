<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psgc_provinces', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('name', 120);
            $table->string('region_code', 10);
            $table->string('region_name', 100);
        });

        Schema::create('psgc_cities', function (Blueprint $table) {
            $table->string('code', 30)->primary();
            $table->string('name', 120);
            $table->string('province_code', 10)->index();
            $table->string('city_class', 20)->default('Municipality'); // City, Municipality, Component City
            $table->foreign('province_code')->references('code')->on('psgc_provinces');
        });

        Schema::create('psgc_barangays', function (Blueprint $table) {
            $table->string('code', 40)->primary();
            $table->string('name', 120);
            $table->string('city_code', 30)->index();
            $table->foreign('city_code')->references('code')->on('psgc_cities');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psgc_barangays');
        Schema::dropIfExists('psgc_cities');
        Schema::dropIfExists('psgc_provinces');
    }
};
