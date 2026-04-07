<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\Section;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnthropWra28Mar2026Seeder extends Seeder
{
    public function run(): void
    {
        // Exact title from the Anthropometric Endline Survey questionnaire dated 28 Mar 2026.
        // A differently-structured survey with the same title exists (ID 6, old seeder).
        // This seeder creates the correctly-structured form following exact Q1–Q21 numbering.
        $title = 'Anthropometric Endline Survey for Evaluating the Impact of Digital Food Vouchers (Phase 2)';

        if (Survey::where('title', $title)->whereHas('sections')->exists()) {
            $this->command->info('Anthropometric survey (28 Mar 2026) already seeded with sections — skipping.');
            return;
        }

        $admin = \App\Models\User::where('role', 'admin')->first();

        // If the old title exists but has no sections, we'll create a new versioned copy
        // so existing data is preserved.
        $finalTitle = Survey::where('title', $title)->exists()
            ? $title . ' — 28 Mar 2026'
            : $title;

        $survey = Survey::create([
            'user_id'            => $admin->id,
            'title'              => $finalTitle,
            'description'        => 'INDIVIDUAL ANTHROPOMETRIC SURVEY, WOMEN OF REPRODUCTIVE AGE (15 to 49 years old) & CHILDREN UNDER-FIVE (0-59 months old). Questionnaire Ctrl No. as assigned. All information obtained in this form is held strictly confidential (RA 10625).',
            'status'             => 'draft',
            'public_token'       => Str::random(32),
            'allow_partial_save' => true,
            'show_progress_bar'  => true,
        ]);

        $o = 0;

        // ── I. HOUSEHOLD INFORMATION ─────────────────────────────────────────────
        $s1 = Section::create([
            'survey_id'  => $survey->id,
            'title'      => 'I. HOUSEHOLD INFORMATION',
            'sort_order' => 1,
        ]);

        $this->q($survey->id, $s1->id, ++$o, 'Q1_CITY_MUN_PROV',
            '1. CITY/MUNICIPALITY, PROVINCE', 'ph_location');

        $this->q($survey->id, $s1->id, ++$o, 'Q2_BARANGAY',
            '2. BARANGAY', 'open_text');

        $this->q($survey->id, $s1->id, ++$o, 'Q3_HCN',
            '3. HOUSEHOLD CTRL. NO. (HCN)', 'open_text', true, 'Identifier 1');

        $this->q($survey->id, $s1->id, ++$o, 'Q4_INTNO',
            '4. SWS INTERVIEW NO. (INTNO)', 'number', true, 'Identifier 2 (1–7989)');

        $q5 = $this->q($survey->id, $s1->id, ++$o, 'Q5_INTERVENTION',
            '5. TYPE OF INTERVENTION IN THE HOUSEHOLD', 'single_choice');
        $this->opts($q5, [
            'CONTROL' => 'Control',
            'PHP3000' => 'PhP 3,000',
            'PHP5000' => 'PhP 5,000',
            'PHP8000' => 'PhP 8,000',
        ]);

        $this->q($survey->id, $s1->id, ++$o, 'Q6_HH_HEAD',
            '6. NAME OF HOUSEHOLD HEAD', 'open_text');

        $this->q($survey->id, $s1->id, ++$o, 'Q7_ADDRESS',
            '7. HOUSE NO., ST. ADDRESS', 'open_text', true, 'Include landmark, if possible');

        $this->q($survey->id, $s1->id, ++$o, 'Q8_MOBILE',
            '8. MOBILE NUMBER', 'open_text', false, 'Or social media account');

        // ── II. MEMBER INFORMATION ───────────────────────────────────────────────
        $s2 = Section::create([
            'survey_id'  => $survey->id,
            'title'      => 'II. MEMBER INFORMATION',
            'sort_order' => 2,
        ]);

        $this->q($survey->id, $s2->id, ++$o, 'Q9_MEMBER_NAME',
            '9. NAME OF HOUSEHOLD MEMBER', 'open_text', true, 'Name of WRA or CU5');

        $this->q($survey->id, $s2->id, ++$o, 'Q10_SWS_MEMBER_NO',
            '10. SWS MEMBER NO.', 'open_text');

        $q11 = $this->q($survey->id, $s2->id, ++$o, 'Q11_MEMBER_CATEGORY',
            '11. HOUSEHOLD MEMBER CATEGORY', 'single_choice');
        $this->opts($q11, [
            'CU5_0_23'  => 'CU5 (0-23 months old)',
            'CU5_24_59' => 'CU5 (24-59 months old)',
            'WRA'       => 'WRA (15-49 years old)',
        ]);

        $this->q($survey->id, $s2->id, ++$o, 'Q12_BIRTHDATE',
            '12. BIRTHDATE', 'date', true, 'dd-mmm-yyyy');

        $this->q($survey->id, $s2->id, ++$o, 'Q13_DATE_MEASUREMENT',
            '13. DATE OF MEASUREMENT', 'date', true, 'dd-mmm-yyyy');

        $this->q($survey->id, $s2->id, ++$o, 'Q14A_AGE_YEARS',
            '14a. AGE in Years', 'number', true, '0-49');

        $this->q($survey->id, $s2->id, ++$o, 'Q14B_AGE_MONTHS',
            '14b. AGE in Months', 'number', true, '0-588');

        $q15 = $this->q($survey->id, $s2->id, ++$o, 'Q15_SEX',
            '15. SEX', 'single_choice');
        $this->opts($q15, ['MALE' => 'Male', 'FEMALE' => 'Female']);

        // WEIGHT (in kg)
        $this->q($survey->id, $s2->id, ++$o, 'Q16A_WT1',
            '16a. Wt1', 'number', true, 'Weight 1 in kg (e.g. 052.5)');

        $this->q($survey->id, $s2->id, ++$o, 'Q16B_WT2',
            '16b. Wt2', 'number', false, 'Weight 2 in kg');

        $this->q($survey->id, $s2->id, ++$o, 'Q16C_WT3',
            '16c. Wt3', 'number', false, 'Weight 3 in kg');

        // LENGTH/HEIGHT (in cm)
        $this->q($survey->id, $s2->id, ++$o, 'Q17A_HT1',
            '17a. Ht1', 'number', true, 'Length/Height 1 in cm (e.g. 082.5)');

        $this->q($survey->id, $s2->id, ++$o, 'Q17B_HT2',
            '17b. Ht2', 'number', false, 'Length/Height 2 in cm');

        $this->q($survey->id, $s2->id, ++$o, 'Q17C_HT3',
            '17c. Ht3', 'number', false, 'Length/Height 3 in cm');

        $q18 = $this->q($survey->id, $s2->id, ++$o, 'Q18_MEAS_STATUS',
            '18. MEASUREMENT STATUS', 'single_choice', true,
            '*1-Completed  2-Partly completed  3-Respondent incapacitated  4-Refused  5-Not at home  6-Away (local)  7-Away (abroad)  8-Other');
        $this->opts($q18, [
            '1' => '1 — Completed',
            '2' => '2 — Partly completed',
            '3' => '3 — Respondent incapacitated',
            '4' => '4 — Refused',
            '5' => '5 — Not at home (away during the survey period)',
            '6' => '6 — Away for an extended period of time working/schooling (local)',
            '7' => '7 — Away for an extended period of time working/schooling (abroad)',
            '8' => '8 — Other, specify',
        ]);

        $this->q($survey->id, $s2->id, ++$o, 'Q18_REMARKS',
            '18. Remarks', 'open_text', false, 'Required if status is Other (8). Also use for any additional remarks.');

        // ── III. SIGNATURE BLOCK ─────────────────────────────────────────────────
        $s3 = Section::create([
            'survey_id'  => $survey->id,
            'title'      => 'III. SIGNATURE BLOCK',
            'sort_order' => 3,
        ]);

        $this->q($survey->id, $s3->id, ++$o, 'Q19_ANTHRO_NAME',
            '19. ANTHROPOMETRIST — Name and Signature', 'open_text');

        $this->q($survey->id, $s3->id, ++$o, 'Q19_ANTHRO_DATE',
            '19. ANTHROPOMETRIST — Date', 'date');

        $this->q($survey->id, $s3->id, ++$o, 'Q20_SUPERVISOR_NAME',
            '20. SUPERVISOR — Name and Signature', 'open_text');

        $this->q($survey->id, $s3->id, ++$o, 'Q20_SUPERVISOR_DATE',
            '20. SUPERVISOR — Date', 'date');

        $this->q($survey->id, $s3->id, ++$o, 'Q21_STUDY_LEADER_NAME',
            '21. STUDY LEADER — Name and Signature', 'open_text');

        $this->q($survey->id, $s3->id, ++$o, 'Q21_STUDY_LEADER_DATE',
            '21. STUDY LEADER — Date', 'date');

        $this->command->info('Created: ' . $finalTitle);
        $this->command->info('Token: ' . $survey->public_token);
        $this->command->info('Total questions: ' . $o);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function q(int $surveyId, int $sectionId, int $sort, string $code,
                       string $label, string $type, bool $required = true,
                       ?string $help = null): Question
    {
        return Question::create([
            'survey_id'     => $surveyId,
            'section_id'    => $sectionId,
            'variable_code' => $code,
            'label'         => $label,
            'type'          => $type,
            'sort_order'    => $sort,
            'is_required'   => $required,
            'help_text'     => $help,
        ]);
    }

    private function opts(Question $question, array $options): void
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
