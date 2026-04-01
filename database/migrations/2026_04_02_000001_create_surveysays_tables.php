<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {

        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->string('public_token', 64)->unique();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('allow_partial_save')->default(false);
            $table->boolean('show_progress_bar')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('display_condition')->nullable(); // e.g. {"question_code":"category","operator":"equals","value":"CU5_0_23"}
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->string('variable_code', 80); // STG export column name
            $table->text('label');
            $table->text('help_text')->nullable();
            $table->enum('type', [
                'single_choice',
                'multi_select',
                'open_text',
                'rating',
                'number',
                'date',
                'time',
                'grid',       // matrix/checklist grid
            ]);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->json('config')->nullable();
            $table->timestamps();
            $table->unique(['survey_id', 'variable_code']);
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->string('option_code', 80);
            $table->string('label', 500);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['question_id', 'option_code']);
        });

        // Grid rows (for grid/matrix questions)
        Schema::create('grid_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->string('row_code', 80);
            $table->string('label', 500);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['question_id', 'row_code']);
        });

        Schema::create('skip_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_question_id')->constrained('questions')->cascadeOnDelete();
            $table->enum('condition_type', [
                'equals', 'not_equals', 'contains',
                'greater_than', 'less_than',
                'selected', 'not_selected'
            ]);
            $table->string('condition_value', 500);
            $table->foreignId('target_question_id')->constrained('questions')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->string('serial', 80)->unique();
            $table->unsignedTinyInteger('status')->default(1); // 1=Complete, 2=Partial
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('respondent_token', 64)->unique();
            $table->timestamps();
        });

        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('response_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->text('value_text')->nullable();
            $table->timestamps();
            $table->unique(['response_id', 'question_id']);
        });

        Schema::create('answer_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_option_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['answer_id', 'question_option_id']);
        });

        // Grid answers: one row per grid cell (question_id × row × column)
        Schema::create('answer_grid_cells', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grid_row_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_option_id')->nullable()->constrained()->nullOnDelete(); // column
            $table->string('cell_value', 500)->nullable();
            $table->timestamps();
            $table->unique(['answer_id', 'grid_row_id', 'question_option_id'], 'answer_grid_unique');
        });

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

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('import_jobs');
        Schema::dropIfExists('answer_grid_cells');
        Schema::dropIfExists('answer_options');
        Schema::dropIfExists('answers');
        Schema::dropIfExists('responses');
        Schema::dropIfExists('skip_rules');
        Schema::dropIfExists('grid_rows');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('surveys');
    }
};
