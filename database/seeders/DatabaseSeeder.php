<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Survey;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\SkipRule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PsgcSeeder::class);

        $admin = User::firstOrCreate(['email' => 'admin@surveysays.test'], [
            'name' => 'Admin', 'password' => Hash::make('Admin@1234!'),
            'role' => 'admin', 'is_active' => true,
        ]);
        User::firstOrCreate(['email' => 'researcher@surveysays.test'], [
            'name' => 'Field Researcher', 'password' => Hash::make('Admin@1234!'),
            'role' => 'researcher', 'is_active' => true,
        ]);

        $survey = Survey::firstOrCreate(['title' => 'IYCF Checklist — REVIVE Pilot Program'], [
            'user_id' => $admin->id,
            'description' => 'Individual Food Consumption — Women of Reproductive Age (15–49 yrs) & Children Under-Five (0–59 months). IFPRI / REVIVE Pilot Program 2026.',
            'status' => 'draft',
            'public_token' => Str::random(32),
            'allow_partial_save' => true,
            'show_progress_bar' => true,
        ]);

        if ($survey->questions()->count() > 0) {
            $this->command->info('Already seeded.');
            return;
        }

        $o = 0;

        // Section I — Household
        $this->q($survey, ++$o, 'LOCATION', 'Location (Province / City / Barangay)', 'ph_location');
        $this->q($survey, ++$o, 'HCN',          'DSWD Household Control No. (HCN)',            'open_text');
        $this->q($survey, ++$o, 'INTNO',        'SWS Interview No. (1–7989)',                  'number', ['min'=>1,'max'=>7989]);
        $this->q($survey, ++$o, 'WGP_CARD',     'WGP Card Number (16 digits)',                 'open_text');
        $this->qChoice($survey, ++$o, 'INTERVENTION', 'Type of Intervention in the Household', 'single_choice', [
            ['C','Control'],['P3','PhP 3,000'],['P5','PhP 5,000'],['P8','PhP 8,000'],
        ]);
        $this->q($survey, ++$o, 'HH_HEAD',      'Name of Household Head',                      'open_text');
        $this->q($survey, ++$o, 'ADDRESS',      'House No., Street Address',                   'open_text');
        $this->q($survey, ++$o, 'MOBILE',       'Mobile Number / Social Media Account',        'open_text', [], false);

        // Section II — Member
        $this->q($survey, ++$o, 'RESP_NAME',    'Name of Respondent (WRA or mother/caregiver of CU5)', 'open_text');
        $this->q($survey, ++$o, 'HH_NAME',      'Name of Household Member (WRA or CU5)',        'open_text');
        $this->q($survey, ++$o, 'SWS_MEMNO',    'SWS Member No.',                               'open_text');
        $this->q($survey, ++$o, 'BIRTHDATE',    'Birthdate (dd-mmm-yyyy)',                      'date');
        $this->q($survey, ++$o, 'AGE',          'Age (years)',                                  'number', ['min'=>0,'max'=>120]);
        $this->qChoice($survey, ++$o, 'SEX', 'Sex', 'single_choice', [['M','Male'],['F','Female']]);
        $this->qChoice($survey, ++$o, 'HH_CATEGORY', 'Household Member Category', 'single_choice', [
            ['CU5_0_23','CU5 (0–23 months old)'],
            ['CU5_24_59','CU5 (24–59 months old)'],
            ['WRA','WRA (15–49 years old)'],
        ]);
        $this->q($survey, ++$o, 'REF_DATE',     'Reference Date',                               'date');
        $this->qChoice($survey, ++$o, 'RECALL_DAY', 'Recall Day', 'single_choice', [['1','Day 1'],['2','Day 2']]);
        $this->q($survey, ++$o, 'REF_DAY',      'Reference Day (day of the week)',              'open_text');
        $this->qChoice($survey, ++$o, 'INT_STATUS', 'Interview Status', 'single_choice', [
            ['1','1 — Completed'],['2','2 — Partly completed'],['3','3 — Respondent incapacitated'],
            ['4','4 — Refused'],['5','5 — Not at home'],['6','6 — Away (local)'],
            ['7','7 — Away (abroad)'],['8','8 — Other'],
        ]);
        $this->q($survey, ++$o, 'INT_REMARKS',  'Interview Remarks',                            'open_text', [], false);

        // Section III — IYCF (CU5 0-23 months)
        $q1 = $this->qChoice($survey, ++$o, 'Q1', 'Was [NAME] ever breastfed?', 'single_choice', [['1','Yes'],['0','No']]);
        $this->qChoice($survey, ++$o, 'Q2_TIMING', 'How long after birth was [NAME] first put to the breast?', 'single_choice', [
            ['IMM','Immediately'],['HRS','In hours (specify below)'],['DYS','In days (specify below)'],
        ]);
        $this->q($survey, ++$o, 'Q2_HOURS',  'Q2 — Hours after birth (if applicable)',         'number', ['min'=>0,'max'=>99], false);
        $this->q($survey, ++$o, 'Q2_DAYS',   'Q2 — Days after birth (if applicable)',          'number', ['min'=>0,'max'=>30], false);
        $this->qChoice($survey, ++$o, 'Q3', 'In the first two days, was [NAME] given anything other than breast milk (water, infant formula, am)?', 'single_choice', [['1','Yes'],['0','No']]);
        $this->qChoice($survey, ++$o, 'Q4', 'Was [NAME] breastfed yesterday during the day or at night?', 'single_choice', [['1','Yes'],['0','No'],['9',"Don't know"]]);
        $q5 = $this->qChoice($survey, ++$o, 'Q5', 'Did [NAME] drink anything from a bottle with a nipple yesterday?', 'single_choice', [['1','Yes'],['0','No'],['9',"Don't know"]]);
        $this->qChoice($survey, ++$o, 'Q6', 'How many times did [NAME] eat any solid/semi-solid/soft foods yesterday?', 'single_choice', [
            ['1','1'],['2','2'],['3','3'],['4','4'],['5','5'],['6','6'],['7P','7 or more'],['9',"Don't know"],
        ]);

        SkipRule::create(['survey_id'=>$survey->id,'source_question_id'=>$q1->id,'condition_type'=>'equals','condition_value'=>'0','target_question_id'=>$q5->id]);

        // Liquid Intake Checklist
        $liq = [
            ['LIQ_01_D','Liquid Intake — BREASTMILK (Day Feeding 6am–5:59pm): Frequency of intake'],
            ['LIQ_01_N','Liquid Intake — BREASTMILK (Night Feeding 6pm–5:59am): Frequency of intake'],
            ['LIQ_02',  'Liquid Intake — WATER (plain water): Frequency of intake'],
            ['LIQ_03_D','Liquid Intake — MILK 1 Infant Formula/follow-on/growing up milk (Day): Frequency'],
            ['LIQ_03_N','Liquid Intake — MILK 1 Infant Formula/follow-on/growing up milk (Night): Frequency'],
            ['LIQ_04_D','Liquid Intake — MILK 2 Whole/filled/recombined milk (Day): Frequency'],
            ['LIQ_04_N','Liquid Intake — MILK 2 Whole/filled/recombined milk (Night): Frequency'],
            ['LIQ_05_D','Liquid Intake — MILK 3 Skimmed/non-fat/low-fat milk (Day): Frequency'],
            ['LIQ_05_N','Liquid Intake — MILK 3 Skimmed/non-fat/low-fat milk (Night): Frequency'],
            ['LIQ_06',  'Liquid Intake — OTHER MILK DRINKS (yogurt drink, Dutchmill, Yakult, etc.): Frequency'],
            ['LIQ_07',  'Liquid Intake — CHOCOLATE DRINKS (Chuckie, Milo, Chocolite, Ovaltine): Frequency'],
            ['LIQ_08',  'Liquid Intake — CAFFEINATED DRINKS (softdrinks, coffee, tea): Frequency'],
            ['LIQ_09',  'Liquid Intake — HERBAL DRINKS (ampalaya, oregano, mint): Frequency'],
            ['LIQ_10',  "Liquid Intake — JUICE (calamansi, Zest-O, Tang, Eight O'clock): Frequency"],
            ['LIQ_11',  'Liquid Intake — CLEAR BROTH (noodles, tinola, nilaga broth): Frequency'],
            ['LIQ_12',  'Liquid Intake — "AM" (sabaw ng sinaing / sabaw ng lugaw na malabnaw): Frequency'],
            ['LIQ_13',  'Liquid Intake — OTHER LIQUIDS (sauce of adobo, menudo, sardines): Frequency'],
            ['LIQ_14',  'Liquid Intake — CEREAL DRINKS (Nevita, Energen, Magnifico, Nutri-Go): Frequency'],
            ['LIQ_15',  'Liquid Intake — VITAMINS & MINERALS (Tiki-Tiki, Cherifer, Appebon): Frequency'],
        ];
        foreach ($liq as [$code, $label]) {
            $this->q($survey, ++$o, $code, $label, 'number', ['min'=>0,'max'=>99], false);
        }

        // Solid Foods DDS Checklist
        $dds = [
            ['DDS_01', 'Solid Foods — GRAINS, ROOTS, TUBERS (porridge, potatoes, sweet potatoes, cassava)'],
            ['DDS_1A', 'Solid Foods — PROCESSED CEREALS/GRAINS/TUBERS (noodles, cereals, pasta, bread)'],
            ['DDS_02', 'Solid Foods — LEGUMES AND NUTS (monggo, mani, green peas, taho, tofu/tokwa)'],
            ['DDS_03', 'Solid Foods — MILK (infant formula, whole, filled, recombined, skimmed)'],
            ['DDS_3A', 'Solid Foods — MILK PRODUCTS (yogurt, cheese, other dairy)'],
            ['DDS_3B', 'Solid Foods — OTHER MILK AND CHOCOLATE DRINKS (Dutchmill, Yakult, Chuckie, Milo)'],
            ['DDS_04', 'Solid Foods — FLESH FOODS (pork, beef, poultry, fish, organ meats)'],
            ['DDS_4A', 'Solid Foods — PROCESSED FLESH FOODS (hotdogs, sausage, tapa, tocino, corned beef)'],
            ['DDS_05', 'Solid Foods — EGGS (chicken, duck, quail, ostrich)'],
            ['DDS_06', 'Solid Foods — VITAMIN A RICH FRUITS & VEGETABLES (ripe mangoes, squash, carrots, malunggay)'],
            ['DDS_07', 'Solid Foods — OTHER FRUITS & VEGETABLES (star apple, chico, eggplant, sayote, patola)'],
            ['DDS_08', 'Solid Foods — OILS AND FATS (mantika, star margarine, gata, butter)'],
            ['DDS_9A', 'Solid Foods — SWEETS, BISCUITS, CAKES, PASTRIES (chocolates, sweets, candies)'],
            ['DDS_9B', 'Solid Foods — SUGAR-SWEETENED BEVERAGES / SSB (softdrinks, powdered juices, Zest-o)'],
            ['DDS_10', 'Solid Foods — SPICES AND CONDIMENTS (toyo, patis, bagoong, catsup)'],
            ['DDS_11', 'Solid Foods — BEVERAGES (coffee, tea)'],
            ['DDS_12', 'Solid Foods — SNACK FOODS (potato chips, Piattos, Tortillos, Dingdong, Muncher)'],
            ['DDS_13', 'Solid Foods — OTHER FOODS (snails, grubs, insects, etc.)'],
            ['DDS_14', 'Solid Foods — MNP Micronutrient Powder (Nutri-foods MNP, Vita Meena MNP, MGM)'],
        ];
        foreach ($dds as [$code, $label]) {
            $this->qChoice($survey, ++$o, $code, $label . ' — DDS (0=No, 1=Yes)', 'single_choice', [
                ['1','1 — Yes, consumed yesterday'],['0','0 — No, not consumed'],
            ], false);
        }

        // Section IV — Additional Questions
        $this->qChoice($survey, ++$o, 'IV_Q1', 'Did your child [NAME] take vitamins/minerals and supplements?', 'single_choice', [['1','Yes'],['0','No']], false);
        $this->q($survey, ++$o, 'IV_Q1_SPEC', 'If yes, specify vitamins/supplements', 'open_text', [], false);

        $ivQ2 = $this->qChoice($survey, ++$o, 'IV_Q2', "Was the amount of food your child [NAME] ate yesterday about usual, less than usual, or more than usual?", 'single_choice', [
            ['U','Usual'],['L','Less than usual'],['M','More than usual'],
        ], false);
        $ivQ2a = $this->qChoice($survey, ++$o, 'IV_Q2A', "Main reason child's food intake was LESS than usual?", 'single_choice', [
            ['1','Sickness'],['2','Short of money'],['3','Traveling'],['4','At a social function / special day'],
            ['5','Sleeping'],['6','Not hungry'],['7','Other reason'],
        ], false);
        $ivQ2b = $this->qChoice($survey, ++$o, 'IV_Q2B', "Main reason child's food intake was MORE than usual?", 'single_choice', [
            ['1','Traveling'],['2','At a social function / special day'],['3','On vacation'],['4','Very hungry'],['5','Some other reason'],
        ], false);
        $this->qChoice($survey, ++$o, 'IV_Q3', "How would you describe your child's [NAME] current dietary habits?", 'single_choice', [
            ['1','No special diet — eats almost everything'],['2','Vegetarian'],['3','Special diet (specify)'],
        ], false);
        $this->q($survey, ++$o, 'IV_Q3_SPEC', 'If special diet, specify', 'open_text', [], false);

        $ivQ3id = Question::where('survey_id',$survey->id)->where('variable_code','IV_Q3')->value('id');
        SkipRule::create(['survey_id'=>$survey->id,'source_question_id'=>$ivQ2->id,'condition_type'=>'equals','condition_value'=>'U','target_question_id'=>$ivQ3id]);
        SkipRule::create(['survey_id'=>$survey->id,'source_question_id'=>$ivQ2->id,'condition_type'=>'equals','condition_value'=>'M','target_question_id'=>$ivQ2b->id]);

        // Section V — Interview Record
        foreach (['1','2'] as $day) {
            $this->q($survey, ++$o, "INT_DATE_{$day}",       "Interview Date — Day {$day}",         'date',      [], false);
            $this->q($survey, ++$o, "INT_TIME_START_{$day}", "Time Started — Day {$day}",           'time',      [], false);
            $this->q($survey, ++$o, "INT_TIME_END_{$day}",   "Time Ended — Day {$day}",             'time',      [], false);
            $this->q($survey, ++$o, "INT_NAME_{$day}",       "Interviewer's Name — Day {$day}",     'open_text', [], false);
            $this->qChoice($survey, ++$o, "INT_STAT_{$day}", "Interview Status — Day {$day}", 'single_choice', [
                ['1','1 — Completed'],['2','2 — Partly completed'],['3','3 — Respondent incapacitated'],
                ['4','4 — Refused'],['5','5 — Not at home'],['6','6 — Away (local)'],
                ['7','7 — Away (abroad)'],['8','8 — Other'],
            ], false);
        }

        $qCount = $survey->questions()->count();
        $rCount = SkipRule::where('survey_id',$survey->id)->count();
        $this->command->info("IYCF survey seeded: {$qCount} questions, {$rCount} skip rules.");
    }

    private function q(Survey $s, int $order, string $code, string $label, string $type, array $config=[], bool $required=true): Question
    {
        return Question::create([
            'survey_id'=>$s->id,'variable_code'=>$code,'label'=>$label,
            'type'=>$type,'sort_order'=>$order,'is_required'=>$required,
            'config'=>empty($config)?null:$config,
        ]);
    }

    private function qChoice(Survey $s, int $order, string $code, string $label, string $type, array $options, bool $required=true): Question
    {
        $q = $this->q($s, $order, $code, $label, $type, [], $required);
        foreach ($options as $i => [$optCode, $optLabel]) {
            QuestionOption::create(['question_id'=>$q->id,'option_code'=>$optCode,'label'=>$optLabel,'sort_order'=>$i]);
        }
        return $q;
    }
}
