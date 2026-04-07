<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Survey;
use App\Models\Section;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\GridRow;
use App\Models\User;

class AnthropometricSurveySeeder extends Seeder
{
    public function run(): void
    {
        $title = 'Anthropometric Endline Survey for Evaluating the Impact of Digital Food Vouchers (Phase 2)';

        if (Survey::where('title', $title)->exists()) {
            $this->command->info('Anthropometric survey already exists, skipping.');
            return;
        }

        $user = User::first();

        $survey = Survey::create([
            'user_id'            => $user?->id,
            'title'              => $title,
            'description'        => 'INDIVIDUAL ANTHROPOMETRIC SURVEY, WOMEN OF REPRODUCTIVE AGE (15 to 49 years old) & CHILDREN UNDER-FIVE (0-59 months old). All information obtained in this form is held strictly confidential and cannot be used for taxation, investigation, or law enforcement purposes (RA 10625).',
            'status'             => 'active',
            'allow_partial_save' => true,
            'show_progress_bar'  => true,
        ]);

        // ── SECTION I: HOUSEHOLD INFORMATION ────────────────────────────────────
        $s1 = Section::create([
            'survey_id'  => $survey->id,
            'title'      => 'I. Household Information',
            'sort_order' => 1,
        ]);

        $this->q($survey->id, $s1->id, 'HH_LOCATION',      'Province / City/Municipality / Barangay',       'ph_location',    10, true);
        $this->q($survey->id, $s1->id, 'HH_HCN',           'DSWD Household Ctrl. No. (HCN)',                'open_text',      20, true,  'Identifier 1');
        $this->q($survey->id, $s1->id, 'HH_INTNO',         'SWS Interview No. (INTNO)',                     'open_text',      30, true,  'Identifier 2 (1–7989)');
        $this->q($survey->id, $s1->id, 'HH_WGP',           'WGP Card Number',                               'open_text',      40, false, 'Identifier 3 (16 digits)');
        $this->q($survey->id, $s1->id, 'QUESTIONNAIRE_NO', 'Questionnaire Ctrl No.',                        'open_text',      45, false);

        $hhInt = $this->q($survey->id, $s1->id, 'HH_INTERVENTION', 'Type of Intervention in the Household', 'single_choice',  50, true);
        $this->options($hhInt, [
            'CONTROL'  => 'Control',
            'PHP3000'  => 'PhP 3,000',
            'PHP5000'  => 'PhP 5,000',
            'PHP8000'  => 'PhP 8,000',
        ]);

        $this->q($survey->id, $s1->id, 'HH_HEAD_NAME', 'Name of Household Head',                            'open_text',      60, true);
        $this->q($survey->id, $s1->id, 'HH_ADDRESS',   'House No., St. Address',                            'open_text',      70, true,  'Include landmark, if possible');
        $this->q($survey->id, $s1->id, 'HH_MOBILE',    'Mobile Number',                                     'open_text',      80, false, 'Or social media account');

        // ── SECTION II: MEMBER INFORMATION & ANTHROPOMETRIC MEASUREMENTS ─────────
        $s2 = Section::create([
            'survey_id'  => $survey->id,
            'title'      => 'II. Member Information',
            'sort_order' => 2,
        ]);

        $this->q($survey->id, $s2->id, 'MEM_NAME',      'Name of Household Member',           'open_text',     10, true,  'Name of WRA or CU5');
        $this->q($survey->id, $s2->id, 'MEM_SWS_NO',    'SWS Member No.',                     'open_text',     20, true);

        $memCat = $this->q($survey->id, $s2->id, 'MEM_CATEGORY', 'Household Member Category', 'single_choice', 30, true);
        $this->options($memCat, [
            'CU5_0_23'  => 'CU5 (0-23 months old)',
            'CU5_24_59' => 'CU5 (24-59 months old)',
            'WRA'       => 'WRA (15-49 years old)',
        ]);

        $this->q($survey->id, $s2->id, 'MEM_BIRTHDATE',        'Birthdate',            'date',          40, true,  'dd-mmm-yyyy');
        $this->q($survey->id, $s2->id, 'MEM_DATE_MEASUREMENT', 'Date of Measurement',  'date',          50, true,  'dd-mmm-yyyy');

        $memSex = $this->q($survey->id, $s2->id, 'MEM_SEX', 'Sex', 'single_choice', 60, true);
        $this->options($memSex, ['MALE' => 'Male', 'FEMALE' => 'Female']);

        $this->q($survey->id, $s2->id, 'MEM_AGE_YEARS',  'Age in Years',   'number', 70, true,  '0–49');
        $this->q($survey->id, $s2->id, 'MEM_AGE_MONTHS', 'Age in Months',  'number', 80, true,  '0–588');

        // Anthropometric Measurements
        $this->q($survey->id, $s2->id, 'WEIGHT_1',  'Weight 1 (kg)',        'number', 90,  true);
        $this->q($survey->id, $s2->id, 'WEIGHT_2',  'Weight 2 (kg)',        'number', 100, false);
        $this->q($survey->id, $s2->id, 'WEIGHT_3',  'Weight 3 (kg)',        'number', 110, false);
        $this->q($survey->id, $s2->id, 'HEIGHT_1',  'Length/Height 1 (cm)', 'number', 120, true);
        $this->q($survey->id, $s2->id, 'HEIGHT_2',  'Length/Height 2 (cm)', 'number', 130, false);
        $this->q($survey->id, $s2->id, 'HEIGHT_3',  'Length/Height 3 (cm)', 'number', 140, false);

        $statusOptions = [
            'S1' => '1 — Completed',
            'S2' => '2 — Partly completed',
            'S3' => '3 — Respondent incapacitated',
            'S4' => '4 — Refused',
            'S5' => '5 — Not at home (away during the survey period)',
            'S6' => '6 — Away for an extended period of time working/schooling (local)',
            'S7' => '7 — Away for an extended period of time working/schooling (abroad)',
            'S8' => '8 — Other',
        ];

        $measStatus = $this->q($survey->id, $s2->id, 'MEAS_STATUS',   'Measurement Status',  'single_choice', 150, true);
        $this->options($measStatus, $statusOptions);
        $this->q($survey->id, $s2->id, 'MEAS_REMARKS', 'Remarks',             'open_text',     160, false);

        // ── SECTION III: MEASUREMENT RECORD ─────────────────────────────────────
        $s3 = Section::create([
            'survey_id'  => $survey->id,
            'title'      => 'VII. Measurement Record',
            'sort_order' => 3,
        ]);

        // Visit 1
        $this->q($survey->id, $s3->id, 'VISIT1_DATE',          'Visit 1 — Date',                 'date',          10, true);
        $this->q($survey->id, $s3->id, 'VISIT1_TIME_START',    'Visit 1 — Time Started',          'time',          20, true);
        $this->q($survey->id, $s3->id, 'VISIT1_TIME_END',      'Visit 1 — Time Ended',            'time',          30, true);
        $this->q($survey->id, $s3->id, 'VISIT1_ANTHROPOMETRIST', 'Visit 1 — Anthropometrist Name', 'open_text',   40, true);

        $v1Status = $this->q($survey->id, $s3->id, 'VISIT1_STATUS',  'Visit 1 — Measurement Status', 'single_choice', 50, true);
        $this->options($v1Status, $statusOptions);
        $this->q($survey->id, $s3->id, 'VISIT1_REMARKS', 'Visit 1 — Remarks', 'open_text', 55, false);

        // Visit 2
        $this->q($survey->id, $s3->id, 'VISIT2_DATE',            'Visit 2 — Date',                   'date',        60, false);
        $this->q($survey->id, $s3->id, 'VISIT2_TIME_START',      'Visit 2 — Time Started',            'time',        70, false);
        $this->q($survey->id, $s3->id, 'VISIT2_TIME_END',        'Visit 2 — Time Ended',              'time',        80, false);
        $this->q($survey->id, $s3->id, 'VISIT2_ANTHROPOMETRIST', 'Visit 2 — Anthropometrist Name',    'open_text',   90, false);

        $v2Status = $this->q($survey->id, $s3->id, 'VISIT2_STATUS',  'Visit 2 — Measurement Status',  'single_choice', 100, false);
        $this->options($v2Status, $statusOptions);
        $this->q($survey->id, $s3->id, 'VISIT2_REMARKS', 'Visit 2 — Remarks', 'open_text', 105, false);

        // Next Visit
        $this->q($survey->id, $s3->id, 'NEXT_VISIT_DATE', 'Next Visit — Date', 'date', 110, false);
        $this->q($survey->id, $s3->id, 'NEXT_VISIT_TIME', 'Next Visit — Time', 'time', 120, false);

        // ── SECTION IV: SIGNATURE BLOCK ─────────────────────────────────────────
        $s4 = Section::create([
            'survey_id'  => $survey->id,
            'title'      => 'VIII. Signature Block',
            'sort_order' => 4,
        ]);

        $this->q($survey->id, $s4->id, 'SIG_RESEARCHER',    'Researcher — Name and Signature',               'open_text', 10, true);
        $this->q($survey->id, $s4->id, 'SIG_FIELD_EDITOR',  'Field Editor — Name and Signature',             'open_text', 20, true);
        $this->q($survey->id, $s4->id, 'SIG_SUPERVISOR',    'Supervisor — Name and Signature',               'open_text', 30, true);
        $this->q($survey->id, $s4->id, 'SIG_OFFICE_EDITOR', 'Office Editor/Validator — Name and Signature',  'open_text', 40, true);
        $this->q($survey->id, $s4->id, 'SIG_ENCODER',       'Office Encoder — Name and Signature',           'open_text', 50, true);

        $this->command->info('Anthropometric Survey created. Token: ' . $survey->public_token);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function q(int $surveyId, int $sectionId, string $code, string $label, string $type,
                       int $sort, bool $required = true, string $helpText = null): Question
    {
        return Question::create([
            'survey_id'     => $surveyId,
            'section_id'    => $sectionId,
            'variable_code' => $code,
            'label'         => $label,
            'type'          => $type,
            'sort_order'    => $sort,
            'is_required'   => $required,
            'help_text'     => $helpText,
        ]);
    }

    private function options(Question $question, array $options): void
    {
        $sort = 0;
        foreach ($options as $code => $label) {
            QuestionOption::create([
                'question_id' => $question->id,
                'option_code' => $code,
                'label'       => $label,
                'sort_order'  => $sort++,
            ]);
        }
    }
}
