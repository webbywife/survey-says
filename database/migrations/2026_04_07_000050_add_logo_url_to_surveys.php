<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('surveys', function (Blueprint $table) {
            $table->string('logo_url', 500)->nullable()->after('show_progress_bar');
        });
    }
    public function down(): void {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn('logo_url');
        });
    }
};
