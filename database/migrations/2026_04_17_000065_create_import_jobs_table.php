<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('import_jobs')) return;
        Schema::create('import_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('original_filename');
            $table->unsignedInteger('row_count_total')->default(0);
            $table->unsignedInteger('row_count_imported')->default(0);
            $table->unsignedInteger('row_count_skipped')->default(0);
            $table->enum('status', ['pending', 'processing', 'done', 'failed'])->default('pending');
            $table->json('error_log')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_jobs');
    }
};
