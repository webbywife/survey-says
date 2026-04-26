<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const SURVEY_TOKEN = 'EVZeBqAvw030EJzy9XQGkGuT5ljLs4c5';

    public function up(): void
    {
        $survey = DB::table('surveys')->where('public_token', self::SURVEY_TOKEN)->first();

        if (!$survey) {
            return;
        }

        $q30 = DB::table('questions')
            ->where('survey_id', $survey->id)
            ->where('label', 'like', '%Fruit juice%')
            ->first();

        $q34 = DB::table('questions')
            ->where('survey_id', $survey->id)
            ->where('label', 'like', '%Clear broth%')
            ->first();

        if (!$q30 || !$q34) {
            return;
        }

        $noOption = DB::table('question_options')
            ->where('question_id', $q30->id)
            ->where('label', 'like', '%No%')
            ->whereRaw('LOWER(label) NOT LIKE "%do not%"')
            ->first();

        if (!$noOption) {
            return;
        }

        $alreadyExists = DB::table('skip_rules')
            ->where('survey_id', $survey->id)
            ->where('source_question_id', $q30->id)
            ->where('condition_value', $noOption->option_code)
            ->where('target_question_id', $q34->id)
            ->exists();

        if ($alreadyExists) {
            return;
        }

        DB::table('skip_rules')->insert([
            'survey_id'          => $survey->id,
            'source_question_id' => $q30->id,
            'condition_type'     => 'equals',
            'condition_value'    => $noOption->option_code,
            'target_question_id' => $q34->id,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
    }

    public function down(): void
    {
        $survey = DB::table('surveys')->where('public_token', self::SURVEY_TOKEN)->first();
        if (!$survey) return;

        $q30 = DB::table('questions')
            ->where('survey_id', $survey->id)
            ->where('label', 'like', '%Fruit juice%')
            ->first();

        $q34 = DB::table('questions')
            ->where('survey_id', $survey->id)
            ->where('label', 'like', '%Clear broth%')
            ->first();

        if (!$q30 || !$q34) return;

        DB::table('skip_rules')
            ->where('survey_id', $survey->id)
            ->where('source_question_id', $q30->id)
            ->where('target_question_id', $q34->id)
            ->delete();
    }
};
