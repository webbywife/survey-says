<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Survey;
use App\Models\Section;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\GridRow;
use App\Models\SkipRule;
use App\Models\User;

class IycfChecklistSeeder extends Seeder
{
    public function run(): void
    {
        // Idempotent — skip if already exists
        if (Survey::where('title', 'IYCF Checklist — REVIVE Pilot Program')->exists()) {
            $this->command->info('IYCF Checklist survey already exists, skipping.');
            return;
        }

        $user = User::first();

        $survey = Survey::create([
            'user_id'            => $user?->id,
            'title'              => 'IYCF Checklist — REVIVE Pilot Program',
            'description'        => 'Examining the Impact of the REVIVE Pilot Program on Dietary Intake of Women of Reproductive Age and Children Under-five',
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

        $this->q($survey->id, $s1->id, 'HH_LOCATION',    'Province / City/Municipality / Barangay',     'ph_location', 10);
        $this->q($survey->id, $s1->id, 'HH_HCN',         'DSWD Household Ctrl. No. (HCN)',              'open_text',   20, false, 'Identifier 1');
        $this->q($survey->id, $s1->id, 'HH_INTNO',       'SWS Interview No. (INTNO)',                   'open_text',   30, false, 'Identifier 2 (1–7989)');
        $this->q($survey->id, $s1->id, 'HH_WGP',         'WGP Card Number',                             'open_text',   40, false, 'Identifier 3 (16 digits)');

        $hhInt = $this->q($survey->id, $s1->id, 'HH_INTERVENTION', 'Type of Intervention in the Household', 'single_choice', 50);
        $this->options($hhInt, ['CONTROL' => 'Control', 'PHP3000' => 'PhP 3,000', 'PHP5000' => 'PhP 5,000', 'PHP8000' => 'PhP 8,000']);

        $this->q($survey->id, $s1->id, 'HH_HEAD_NAME', 'Name of Household Head',                          'open_text', 60);
        $this->q($survey->id, $s1->id, 'HH_ADDRESS',   'House No., St. Address',                          'open_text', 70, true, 'Include landmark, if possible');
        $this->q($survey->id, $s1->id, 'HH_MOBILE',    'Mobile Number',                                   'open_text', 80, false, 'Or social media account');

        // ── SECTION II: MEMBER INFORMATION ──────────────────────────────────────
        $s2 = Section::create([
            'survey_id'  => $survey->id,
            'title'      => 'II. Member Information',
            'sort_order' => 2,
        ]);

        $this->q($survey->id, $s2->id, 'MEM_RESPONDENT', 'Name of Respondent',        'open_text', 10, true, 'Name of interviewee: WRA or mother/primary caregiver of CU5');
        $this->q($survey->id, $s2->id, 'MEM_NAME',        'Name of Household Member', 'open_text', 20, true, 'Name of WRA or CU5');
        $this->q($survey->id, $s2->id, 'MEM_SWS_NO',      'SWS Member No.',           'open_text', 30);
        $this->q($survey->id, $s2->id, 'MEM_BIRTHDATE',   'Birthdate',                'date',       40, true, 'dd-mmm-yyyy');
        $this->q($survey->id, $s2->id, 'MEM_AGE',         'Age',                      'number',     50, true, 'In years');

        $memSex = $this->q($survey->id, $s2->id, 'MEM_SEX', 'Sex', 'single_choice', 60);
        $this->options($memSex, ['MALE' => 'Male', 'FEMALE' => 'Female']);

        $memCat = $this->q($survey->id, $s2->id, 'MEM_CATEGORY', 'Household Member Category', 'single_choice', 70);
        $this->options($memCat, [
            'CU5_0_23'  => 'CU5 (0-23 months old)',
            'CU5_24_59' => 'CU5 (24-59 months old)',
            'WRA'       => 'WRA (15-49 years old)',
        ]);

        $this->q($survey->id, $s2->id, 'MEM_REF_DATE',   'Reference Date',              'date',         80, true, 'dd-mmm-yyyy');
        $memRecall = $this->q($survey->id, $s2->id, 'MEM_RECALL_DAY', 'Recall Day',     'single_choice', 90);
        $this->options($memRecall, ['DAY1' => 'Day 1', 'DAY2' => 'Day 2']);

        $this->q($survey->id, $s2->id, 'MEM_REF_DAY',    'Reference Day',               'open_text', 100, true, 'Day of the week');
        $this->q($survey->id, $s2->id, 'MEM_INT_STATUS',  'Interview Status',            'open_text', 110, false);
        $this->q($survey->id, $s2->id, 'MEM_REMARKS',     'Remarks',                    'open_text', 120, false);

        // ── SECTION III: IYCF PRACTICES ─────────────────────────────────────────
        $s3 = Section::create([
            'survey_id'   => $survey->id,
            'title'       => 'III. Infant and Young Child Feeding Practices',
            'description' => 'FOR CU5 AGED 0-23 MONTHS — Skip for CU5 aged 24–59 months and WRA',
            'sort_order'  => 3,
        ]);

        $q1 = $this->q($survey->id, $s3->id, 'Q1_BREASTFED', 'Was [NAME] ever breastfed?', 'single_choice', 10, false);
        $this->options($q1, ['YES' => 'Yes', 'NO' => 'No']);

        $q2 = $this->q($survey->id, $s3->id, 'Q2_BREAST_TIMING', 'How long after birth was [NAME] first put to the breast?', 'single_choice', 20, false);
        $this->options($q2, ['IMMEDIATELY' => 'Immediately', 'HOURS' => 'Hours', 'DAYS' => 'Days']);

        $this->q($survey->id, $s3->id, 'Q2_HOURS', 'Number of hours after birth', 'number', 25, false);
        $this->q($survey->id, $s3->id, 'Q2_DAYS',  'Number of days after birth',  'number', 27, false);

        $q3 = $this->q($survey->id, $s3->id, 'Q3_OTHER_DRINK',
            'In the first two days after delivery, was [NAME] given anything other than breast milk to eat or drink – anything at all like water, infant formula, or am (rice water)?',
            'single_choice', 30, false);
        $this->options($q3, ['YES' => 'Yes', 'NO' => 'No']);

        $q4 = $this->q($survey->id, $s3->id, 'Q4_BREASTFED_YEST', 'Was [NAME] breastfed yesterday during the day or at night?', 'single_choice', 40, false);
        $this->options($q4, ['YES' => 'Yes', 'NO' => 'No', 'DK' => "Don't know"]);

        $q5 = $this->q($survey->id, $s3->id, 'Q5_BOTTLE', 'Did [NAME] drink anything from a bottle with a nipple yesterday during the day or at night?', 'single_choice', 50, false);
        $this->options($q5, ['YES' => 'Yes', 'NO' => 'No', 'DK' => "Don't know"]);

        $q6 = $this->q($survey->id, $s3->id, 'Q6_SOLID_FREQ', 'How many times did [NAME] eat any solid, semi-solid or soft foods yesterday during the day or night?', 'single_choice', 60, false);
        $this->options($q6, ['TIMES' => '1–6 times (specify number)', 'SEVEN_PLUS' => '7 or more', 'DK' => "Don't know"]);

        $this->q($survey->id, $s3->id, 'Q6_SOLID_COUNT', 'Number of times (if 1–6)', 'number', 65, false);

        // Skip rule: Q1_BREASTFED = NO → jump to Q5_BOTTLE
        SkipRule::create([
            'survey_id'          => $survey->id,
            'source_question_id' => $q1->id,
            'condition_type'     => 'equals',
            'condition_value'    => 'NO',
            'target_question_id' => $q5->id,
        ]);

        // ── SECTION IV: CHECKLIST OF LIQUID INTAKE ──────────────────────────────
        $s4 = Section::create([
            'survey_id'  => $survey->id,
            'title'      => 'Checklist of Liquid Intake',
            'sort_order' => 4,
        ]);

        $liq = $this->q($survey->id, $s4->id, 'LIQ_INTAKE', 'Checklist of Liquid Intake', 'grid', 10, false,
            'Record the frequency of intake for each liquid. For breastmilk and milk types, record day and night feedings separately.');
        $this->options($liq, ['FREQ' => 'Frequency of Intake']);
        $this->gridRows($liq, [
            'R01_BM_DAY'     => 'BREASTMILK — Day Feeding (6:00 am – 5:59 pm)',
            'R01_BM_NIGHT'   => 'BREASTMILK — Night Feeding (6:00 pm – 5:59 am)',
            'R02_WATER'      => 'WATER — Plain water only (do not include water used to dilute milk)',
            'R03_MILK1_DAY'  => 'MILK 1 (Infant Formula, follow-on & growing up milk) — Day Feeding (6:00 am – 5:59 pm)',
            'R03_MILK1_NIGHT'=> 'MILK 1 — Night Feeding (6:00 pm – 5:59 am)',
            'R04_MILK2_DAY'  => 'MILK 2 (Other milk e.g. whole, filled, recombined/reconstituted family/adults milk) — Day Feeding (6:00 am – 5:59 pm)',
            'R04_MILK2_NIGHT'=> 'MILK 2 — Night Feeding (6:00 pm – 5:59 am)',
            'R05_MILK3_DAY'  => 'MILK 3 (Other milk e.g. skimmed/non-fat, low-fat milk) — Day Feeding (6:00 am – 5:59 pm)',
            'R05_MILK3_NIGHT'=> 'MILK 3 — Night Feeding (6:00 pm – 5:59 am)',
            'R06_OTHER_MILK' => 'OTHER MILK DRINKS — Yogurt drink, or any other probiotic drink e.g. Dutchmill, Yakult, Bear Brand Probiotic Drink',
            'R07_CHOCO'      => 'CHOCOLATE DRINKS — Chuckie, Milo, Chocolite, Ovaltine, etc.',
            'R08_CAFFEINE'   => 'CAFFEINATED DRINKS — Softdrinks, coffee, tea, etc.',
            'R09_HERBAL'     => 'HERBAL DRINKS — Herbal extracts from leaves e.g. ampalaya, oregano, mint, etc.',
            'R10_JUICE'      => 'JUICE — Fresh juice e.g. calamansi or orange juice, or ready-to-drink juice drinks e.g. Zest-O, Fun chum, Tang, Eight O\'clock',
            'R11_BROTH'      => 'CLEAR BROTH — e.g. noodles, tinola, nilaga broth etc.',
            'R12_AM'         => '"AM" — Sabaw ng sinaing or sabaw ng lugaw na malabnaw',
            'R13_OTHER'      => 'OTHER LIQUIDS — Any other liquids not classified above e.g. sauce of adobo, menudo, afritada, sardines etc.',
            'R14_CEREAL'     => 'CEREAL DRINKS — e.g. Nevita Cereal Milk Drink, Energen, Magnifico, Nutri-Go, Bear Brand Busog-Lusog',
            'R15_VITAMINS'   => 'VITAMINS & MINERALS — Tiki-Tiki, Cherifer, Appebon, and the like',
        ]);

        // ── SECTION V: CHECKLIST OF SOLID, SEMI-SOLID, AND SOFT FOODS ───────────
        $s5 = Section::create([
            'survey_id'  => $survey->id,
            'title'      => 'Checklist of Solid, Semi-Solid, and Soft Foods Intake',
            'sort_order' => 5,
        ]);

        $solid = $this->q($survey->id, $s5->id, 'SOLID_DDS',
            'Checklist of Solid, Semi-Solid, and Soft Foods Intake',
            'grid', 10, false,
            'DDS: Write "1" if the food group was consumed yesterday, "0" if not. Write the total at the bottom.');
        $this->options($solid, ['DDS' => 'DDS — Write "1" or "0"']);
        $this->gridRows($solid, [
            'R01_GRAINS'      => 'GRAINS, ROOTS, AND TUBERS — Porridge, or other food made from grains, white potatoes, sweet potatoes, cassava or any other roots and tubers',
            'R01A_PROC_GRAIN' => 'PROCESSED CEREALS, GRAINS, AND TUBERS — Noodles, cereals, pasta, plain biscuits, bread and baked products',
            'R02_LEGUMES'     => 'LEGUMES AND NUTS — Beans, seeds, dried peas, dried sitaw seeds, lentils, nuts e.g. monggo, mani, green peas, taho, tofu/tokwa, toyo (as ulam)',
            'R03_MILK'        => 'MILK — Infant formula, whole, filled, recombined/reconstituted family/adults milk, skimmed/non-fat, low fat milk',
            'R03A_MILK_PROD'  => 'MILK PRODUCTS — Yogurt, cheese and other dairy products',
            'R03B_MILK_CHOCO' => 'OTHER MILK AND CHOCOLATE DRINKS — Yogurt drink, probiotic drink e.g. Dutchmill, Yakult, Bear Brand Probiotic Drink; chocolate drinks e.g. Chuckie, Milo, Chocolite, Ovaltine',
            'R04_FLESH'       => 'FLESH FOODS — Meats like pork, beef, poultry, fish including organ meats',
            'R04A_PROC_FLESH' => 'PROCESSED FLESH FOODS — Hotdogs, sausage, tapa, tocino, ham, bacon, longganisa, corned beef, other canned meats',
            'R05_EGGS'        => 'EGGS — Eggs (chicken, duck, quail, ostrich)',
            'R06_VITA_FV'     => 'VITAMIN A RICH FRUITS AND VEGETABLES — FRUITS: ripe mangoes, ripe papayas, melon, tomato (raw), tiesa; VEGETABLES: squash, carrots, ampalaya, lettuce (except iceberg), broccoli, malunggay, talbos ng kamote, saluyot, kulitis, dahon ng sili, alugbati, kangkong, pechay etc.',
            'R07_OTHER_FV'    => 'OTHER FRUITS AND VEGETABLES — FRUITS: star apple, chico, atis, lanzones, guava, starfruit, rambutan, tambis/makopa, tamarind, pomelo, banana, unripe mangoes; VEGETABLES: eggplant, sayote, patola, upo, kundol, sitaw (in pod) etc.',
            'R08_OILS'        => 'OILS AND FATS — Any oil, fats or butter, gata (ex. mantika, star margarine)',
            'R09A_SWEETS'     => 'SWEETS, BISCUITS, CAKES AND PASTRIES — Chocolates, sweets, candies, pastries, cakes, cookies or biscuits with sweet fillings (ex. Rebisco cream sandwich, Stick-o)',
            'R09B_SSB'        => 'SUGAR-SWEETENED BEVERAGES (SSB) — Softdrinks, sweet tea, powdered juices like Tang, Eight o\'clock, Nestea; juice drinks like Zest-o, Funchum, and Big',
            'R10_SPICES'      => 'SPICES AND CONDIMENTS — Condiments and spices for flavor e.g. toyo, patis, bagoong, catsup',
            'R11_BEVERAGES'   => 'BEVERAGES — Coffee, tea and the like',
            'R12_SNACK'       => 'SNACK FOODS — Potato chips, corn snacks like Piattos, Tortillos, Dingdong, Muncher and the like',
            'R13_OTHER'       => 'OTHER FOODS — Any other foods not classified above e.g. snails, grubs, insects, etc.',
            'R14_MNP'         => 'MNP — Micronutrient Powder (Nutri-foods MNP, Vita Meena MNP, Vita Nutrient Mix, Micronutrient Growth Mix (MGM))',
        ]);

        $this->q($survey->id, $s5->id, 'SOLID_DDS_TOTAL', 'DDS Total', 'number', 20, false,
            'Sum of all food groups consumed (total of 1s)');

        // ── SECTION VI: ADDITIONAL QUESTIONS ────────────────────────────────────
        $s6 = Section::create([
            'survey_id'  => $survey->id,
            'title'      => 'IV. Additional Questions',
            'sort_order' => 6,
        ]);

        $aq1 = $this->q($survey->id, $s6->id, 'AQ1_VITAMINS',
            'Did your child [NAME] take vitamins/minerals and supplements?', 'single_choice', 10, false);
        $this->options($aq1, ['YES' => 'Yes, please specify', 'NO' => 'No']);
        $this->q($survey->id, $s6->id, 'AQ1_VITAMINS_SPEC', 'Please specify vitamins/minerals/supplements', 'open_text', 15, false);

        $aq2 = $this->q($survey->id, $s6->id, 'AQ2_FOOD_AMT',
            'Was the amount of food your child [NAME] ate yesterday about usual, less than usual, or more than usual?',
            'single_choice', 20, false);
        $this->options($aq2, ['USUAL' => 'Usual', 'LESS' => 'Less than usual', 'MORE' => 'More than usual']);

        $aq2a = $this->q($survey->id, $s6->id, 'AQ2A_LESS',
            'What is the main reason why the amount of your child\'s [NAME] food intake yesterday was less than usual?',
            'multi_select', 30, false);
        $this->options($aq2a, [
            'SICK'    => 'Sickness',
            'MONEY'   => 'Short of money',
            'TRAVEL'  => 'Traveling',
            'SOCIAL'  => 'At a social function, a special meal, or on a special day',
            'SLEEP'   => 'Sleeping',
            'HUNGRY'  => 'Not hungry',
            'OTHER'   => 'Other reason',
        ]);
        $this->q($survey->id, $s6->id, 'AQ2A_OTHER', 'Please specify other reason (less than usual)', 'open_text', 35, false);

        $aq2b = $this->q($survey->id, $s6->id, 'AQ2B_MORE',
            'What is the main reason why the amount your child\'s [NAME] food intake yesterday was more than usual?',
            'multi_select', 40, false);
        $this->options($aq2b, [
            'TRAVEL'   => 'Traveling',
            'SOCIAL'   => 'At a social function, special meal, or on a special day',
            'VACATION' => 'On vacation',
            'HUNGRY'   => 'Very hungry',
            'OTHER'    => 'Some other reason',
        ]);
        $this->q($survey->id, $s6->id, 'AQ2B_OTHER', 'Please specify other reason (more than usual)', 'open_text', 45, false);

        $aq3 = $this->q($survey->id, $s6->id, 'AQ3_DIET',
            'How would you describe your child\'s [NAME] current dietary habits?', 'single_choice', 50, false);
        $this->options($aq3, [
            'NORMAL'  => 'No special diet, she eats almost everything',
            'VEG'     => 'Vegetarian',
            'SPECIAL' => 'Special diet (specify)',
        ]);
        $this->q($survey->id, $s6->id, 'AQ3_SPECIAL', 'Please specify special diet', 'open_text', 55, false);

        // Skip rules for Section IV
        // AQ2_FOOD_AMT = Usual → skip to AQ3_DIET
        SkipRule::create([
            'survey_id'          => $survey->id,
            'source_question_id' => $aq2->id,
            'condition_type'     => 'equals',
            'condition_value'    => 'USUAL',
            'target_question_id' => $aq3->id,
        ]);
        // AQ2_FOOD_AMT = More than usual → skip to AQ2B
        SkipRule::create([
            'survey_id'          => $survey->id,
            'source_question_id' => $aq2->id,
            'condition_type'     => 'equals',
            'condition_value'    => 'MORE',
            'target_question_id' => $aq2b->id,
        ]);

        // ── SECTION VII: INTERVIEW RECORD ───────────────────────────────────────
        $s7 = Section::create([
            'survey_id'  => $survey->id,
            'title'      => 'V. Interview Record',
            'sort_order' => 7,
        ]);

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

        $this->q($survey->id, $s7->id, 'INT1_DATE',    'Day 1 — Date',               'date',         10);
        $this->q($survey->id, $s7->id, 'INT1_START',   'Day 1 — Time Started',        'time',         20);
        $this->q($survey->id, $s7->id, 'INT1_END',     'Day 1 — Time Ended',          'time',         30);
        $this->q($survey->id, $s7->id, 'INT1_NAME',    'Day 1 — Interviewer\'s Name', 'open_text',    40);
        $int1s = $this->q($survey->id, $s7->id, 'INT1_STATUS', 'Day 1 — Interview Status', 'single_choice', 50);
        $this->options($int1s, $statusOptions);
        $this->q($survey->id, $s7->id, 'INT1_REMARKS', 'Day 1 — Remarks',             'open_text',    55, false);

        $this->q($survey->id, $s7->id, 'INT2_DATE',    'Day 2 — Date',                'date',         60, false);
        $this->q($survey->id, $s7->id, 'INT2_START',   'Day 2 — Time Started',        'time',         70, false);
        $this->q($survey->id, $s7->id, 'INT2_END',     'Day 2 — Time Ended',          'time',         80, false);
        $this->q($survey->id, $s7->id, 'INT2_NAME',    'Day 2 — Interviewer\'s Name', 'open_text',    90, false);
        $int2s = $this->q($survey->id, $s7->id, 'INT2_STATUS', 'Day 2 — Interview Status', 'single_choice', 100, false);
        $this->options($int2s, $statusOptions);
        $this->q($survey->id, $s7->id, 'INT2_REMARKS', 'Day 2 — Remarks',             'open_text',   105, false);

        $this->q($survey->id, $s7->id, 'INT_NEXT_DATE', 'Next Visit — Date', 'date', 110, false);
        $this->q($survey->id, $s7->id, 'INT_NEXT_TIME', 'Next Visit — Time', 'time', 120, false);

        // ── SECTION VIII: SIGNATURE BLOCK ───────────────────────────────────────
        $s8 = Section::create([
            'survey_id'  => $survey->id,
            'title'      => 'VIII. Signature Block',
            'sort_order' => 8,
        ]);

        $this->q($survey->id, $s8->id, 'SIG_RESEARCHER',    'Researcher — Name and Signature',            'open_text', 10);
        $this->q($survey->id, $s8->id, 'SIG_FIELD_EDITOR',  'Field Editor — Name and Signature',          'open_text', 20);
        $this->q($survey->id, $s8->id, 'SIG_SUPERVISOR',    'Supervisor — Name and Signature',            'open_text', 30);
        $this->q($survey->id, $s8->id, 'SIG_OFFICE_EDITOR', 'Office Editor/Validator — Name and Signature', 'open_text', 40);
        $this->q($survey->id, $s8->id, 'SIG_ENCODER',       'Office Encoder — Name and Signature',        'open_text', 50);

        $this->command->info('IYCF Checklist survey created. Token: ' . $survey->public_token);
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

    private function gridRows(Question $question, array $rows): void
    {
        $sort = 0;
        foreach ($rows as $code => $label) {
            GridRow::create([
                'question_id' => $question->id,
                'row_code'    => $code,
                'label'       => $label,
                'sort_order'  => $sort++,
            ]);
        }
    }
}
