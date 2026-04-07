<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\SkipRule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IycfCu5Seeder extends Seeder
{
    public function run(): void
    {
        $admin = \App\Models\User::where('role', 'admin')->first();

        $survey = Survey::firstOrCreate(['title' => 'IYCF Checklist — CU5 (0–23 Months)'], [
            'user_id'            => $admin->id,
            'description'        => 'Infant and Young Child Feeding (IYCF) — For CU5 aged 0–23 months only (skip for CU5 aged 24–59 months and WRA). REVIVE Pilot Program 2026.',
            'status'             => 'draft',
            'public_token'       => Str::random(32),
            'allow_partial_save' => true,
            'show_progress_bar'  => true,
        ]);

        if ($survey->questions()->count() > 0) {
            $this->command->info('IYCF CU5 survey already seeded — skipping.');
            return;
        }

        $o = 0;

        // ── I. HOUSEHOLD INFORMATION ──────────────────────────────────────────
        $this->q($survey, ++$o, 'Q1_CITY_MUN_PROV', '1. CITY/MUNICIPALITY, PROVINCE',                              'open_text');
        $this->q($survey, ++$o, 'Q2_BARANGAY',       '2. BARANGAY',                                                'open_text');
        $this->q($survey, ++$o, 'Q3_HCN',            '3. HOUSEHOLD CTRL. NO. (HCN) — Identifier 1',               'open_text');
        $this->q($survey, ++$o, 'Q4_INTNO',          '4. SWS INTERVIEW NO. (INTNO) — Identifier 2 (1–7989)',      'number', ['min'=>1,'max'=>7989]);
        $this->qChoice($survey, ++$o, 'Q5_INTERVENTION', '5. TYPE OF INTERVENTION IN THE HOUSEHOLD', 'single_choice', [
            ['CONTROL','Control'],['PHP3000','PhP 3,000'],['PHP5000','PhP 5,000'],['PHP8000','PhP 8,000'],
        ]);
        $this->q($survey, ++$o, 'Q6_HH_HEAD', '6. NAME OF HOUSEHOLD HEAD',                                        'open_text');
        $this->q($survey, ++$o, 'Q7_ADDRESS',  '7. HOUSE NO., ST. ADDRESS (include landmark, if possible)',        'open_text');
        $this->q($survey, ++$o, 'Q8_MOBILE',   '8. MOBILE NUMBER (or social media account)',                      'open_text', [], false);

        // ── II. MEMBER INFORMATION ────────────────────────────────────────────
        $this->q($survey, ++$o, 'Q9_RESPONDENT',  '9. NAME OF RESPONDENT (mother/primary caregiver of CU5)',      'open_text');
        $this->q($survey, ++$o, 'Q10_CHILD_NAME', '10. NAME OF 0–23 MONTH OLD CHILD',                             'open_text');
        $this->q($survey, ++$o, 'Q11_MEMBER_NO',  '11. SWS MEMBER NO.',                                           'open_text');
        $this->q($survey, ++$o, 'Q12_BIRTHDATE',  '12. BIRTHDATE (dd-mmm-yyyy)',                                  'date');
        $this->q($survey, ++$o, 'Q13_REF_DATE',   '13. REFERENCE DATE (dd-mmm-yyyy)',                             'date');
        $this->q($survey, ++$o, 'Q14_AGE_MONTHS', '14. AGE IN MONTHS',                                            'number', ['min'=>0,'max'=>23]);
        $this->qChoice($survey, ++$o, 'Q15_SEX', '15. SEX', 'single_choice', [
            ['M','Male'],['F','Female'],
        ]);
        $this->qChoice($survey, ++$o, 'Q16_REF_DAY', '16. REFERENCE DAY (Day of the Week)', 'single_choice', [
            ['MON','Monday'],['TUE','Tuesday'],['WED','Wednesday'],['THU','Thursday'],
            ['FRI','Friday'],['SAT','Saturday'],['SUN','Sunday'],
        ]);
        $this->qChoice($survey, ++$o, 'Q17_INT_STATUS', '17. INTERVIEW STATUS', 'single_choice', [
            ['1','1 — Completed'],['2','2 — Partly completed'],['3','3 — Respondent incapacitated'],
            ['4','4 — Refused'],['5','5 — Not at home'],['6','6 — Away (local)'],
            ['7','7 — Away (abroad)'],['8','8 — Other'],
        ], false);
        $this->q($survey, ++$o, 'Q17_REMARKS', '17. Remarks', 'open_text', [], false);

        // ── III. BREASTFEEDING / BOTTLE FEEDING PRACTICES ────────────────────
        $q18 = $this->qChoice($survey, ++$o, 'Q18_EVER_BF', '18. Was [NAME] ever breastfed?', 'single_choice', [
            ['1','Yes'],['0','No'],
        ]);
        $this->qChoice($survey, ++$o, 'Q19_BF_TIMING', '19. How long after birth was [NAME] first put to the breast?', 'single_choice', [
            ['IMM','Immediately'],['HRS','In hours (specify below)'],['DYS','In days (specify below)'],
        ]);
        $this->q($survey, ++$o, 'Q19_HOURS', '19. Hours after birth (specify)', 'number', ['min'=>0,'max'=>99], false);
        $this->q($survey, ++$o, 'Q19_DAYS',  '19. Days after birth (specify)',  'number', ['min'=>0,'max'=>30], false);
        $this->qChoice($survey, ++$o, 'Q20_NON_BM', '20. In the first two days after delivery, was [NAME] given anything other than breast milk to eat or drink — anything at all like water, infant formula, or am (rice water)?', 'single_choice', [
            ['1','Yes'],['0','No'],
        ]);
        $this->qChoice($survey, ++$o, 'Q21_BF_YEST', '21. Was [NAME] breastfed yesterday during the day or at night?', 'single_choice', [
            ['1','Yes'],['0','No'],['9',"Don't know"],
        ]);
        $q22 = $this->qChoice($survey, ++$o, 'Q22_BOTTLE', '22. Did [NAME] drink anything from a bottle with a nipple yesterday during the day or at night?', 'single_choice', [
            ['1','Yes'],['0','No'],['9',"Don't know"],
        ]);
        $this->qChoice($survey, ++$o, 'Q23_SOLID_FREQ', '23. How many times did [NAME] eat any solid, semi-solid or soft foods yesterday during the day or night?', 'single_choice', [
            ['1','1'],['2','2'],['3','3'],['4','4'],['5','5'],['6','6'],['7P','7 or more'],['9',"Don't know"],
        ]);

        // Skip: Q18 = No → go to Q22
        SkipRule::create([
            'survey_id'          => $survey->id,
            'source_question_id' => $q18->id,
            'condition_type'     => 'equals',
            'condition_value'    => '0',
            'target_question_id' => $q22->id,
        ]);

        // ── IV. LIQUID INTAKE ─────────────────────────────────────────────────
        $this->q($survey, ++$o, 'Q24A_BM_DAY',     '24a. BREASTMILK — Day Feeding (6:00 am – 5:59 pm): Frequency of intake (0–12)',                          'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q24B_BM_NIGHT',   '24b. BREASTMILK — Night Feeding (6:00 pm – 5:59 am): Frequency of intake (0–12)',                        'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q25_WATER',        '25. WATER — Plain water only (do not include water used to dilute milk): Frequency of intake (0–12)',    'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q26A_MILK1_DAY',   '26a. MILK 1 (Infant Formula, follow-on & growing up milk) — Day Feeding (6:00 am – 5:59 pm): Frequency (0–12)',  'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q26B_MILK1_NIGHT', '26b. MILK 1 (Infant Formula, follow-on & growing up milk) — Night Feeding (6:00 pm – 5:59 am): Frequency (0–12)', 'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q27A_MILK2_DAY',   '27a. MILK 2 (Whole, filled, recombined/reconstituted family/adults milk) — Day Feeding: Frequency (0–12)',          'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q27B_MILK2_NIGHT', '27b. MILK 2 (Whole, filled, recombined/reconstituted family/adults milk) — Night Feeding: Frequency (0–12)',        'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q28A_MILK3_DAY',   '28a. MILK 3 (Skimmed/non-fat, low-fat milk) — Day Feeding: Frequency (0–12)',                          'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q28B_MILK3_NIGHT', '28b. MILK 3 (Skimmed/non-fat, low-fat milk) — Night Feeding: Frequency (0–12)',                        'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q29_OTHER_MILK',   '29. OTHER MILK DRINKS (yogurt drink, Dutchmill, Yakult, Bear Brand Probiotic Drink): Frequency (0–12)', 'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q30_CHOCO',        '30. CHOCOLATE DRINKS (Chuckie, Milo, Chocolite, Ovaltine): Frequency (0–12)',                           'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q31_CAFF',         '31. CAFFEINATED DRINKS (softdrinks, coffee, tea): Frequency (0–12)',                                    'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q32A_HERBAL',      '32. HERBAL DRINKS (ampalaya, oregano, mint, and other herbal extracts from leaves): Frequency (0–12)',  'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q32B_JUICE',       "32. JUICE (calamansi, orange juice, Zest-O, Fun chum, Tang, Eight O'clock): Frequency (0–12)",          'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q33_BROTH',        '33. CLEAR BROTH (noodles, tinola, nilaga broth): Frequency (0–12)',                                     'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q34_AM',           '34. "AM" (Sabaw ng sinaing o sabaw ng lugaw na malabnaw): Frequency (0–12)',                            'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q35_OTHER_LIQ',   '35. OTHER LIQUIDS (sauce of adobo, menudo, afritada, sardines, etc.): Frequency (0–12)',                 'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q36_CEREAL_DRK',  '36. CEREAL DRINKS (Nevita, Energen, Magnifico, Nutri-Go, Bear Brand Busog-Lusog): Frequency (0–12)',    'number', ['min'=>0,'max'=>12], false);
        $this->q($survey, ++$o, 'Q37_VIT_MIN',     '37. VITAMINS & MINERALS (Tiki-Tiki, Cherifer, Appebon): Frequency (0–12)',                               'number', ['min'=>0,'max'=>12], false);

        // ── V. CHECKLIST OF SOLID, SEMI-SOLID, AND SOFT FOODS ────────────────
        $dds = [
            ['Q38_GRAINS',      '38. GRAINS, ROOTS, AND TUBERS — Porridge, or other food made from grains, white potatoes, sweet potatoes, cassava or any other roots and tubers'],
            ['Q38A_PROC_CEREAL','38a. PROCESSED CEREALS, GRAINS, AND TUBERS — Noodles, cereals, pasta, plain biscuits, bread and baked products'],
            ['Q39_LEGUMES',     '39. LEGUMES AND NUTS — Monggo, mani, green peas, dried sitaw seeds, lentils, nuts; taho, tofu/tokwa, toyo (as ulam)'],
            ['Q40_MILK_DDS',    '40. MILK — Infant formula, whole, filled, recombined/reconstituted family/adults milk, skimmed/non-fat, low fat milk'],
            ['Q40A_MILK_PROD',  '40a. MILK PRODUCTS — Yogurt, cheese and other dairy products'],
            ['Q40B_MILK_CHOCO', '40b. OTHER MILK AND CHOCOLATE DRINKS — Dutchmill, Yakult, Bear Brand Probiotic Drink; Chuckie, Milo, Chocolite, Ovaltine'],
            ['Q41_FLESH',       '41. FLESH FOODS — Pork, beef, poultry, fish including organ meats'],
            ['Q41A_PROC_FLESH', '41a. PROCESSED FLESH FOODS — Hotdogs, sausage, tapa, tocino, ham, bacon, longganisa, corned beef, other canned meats'],
            ['Q42_EGGS',        '42. EGGS — Chicken, duck, quail, ostrich'],
            ['Q43_VITA_FV',     '43. VITAMIN A RICH FRUITS AND VEGETABLES — Ripe mangoes, ripe papayas, melon, tomato (raw), tiesa; squash, carrots, ampalaya, lettuce, broccoli, malunggay, talbos ng kamote, saluyot, kulitis, dahon ng sili, alugbati, kangkong, pechay'],
            ['Q44_OTHER_FV',    '44. OTHER FRUITS AND VEGETABLES — Star apple, chico, atis, lanzones, guava, starfruit, rambutan, tambis/makopa, tamarind, pomelo, banana, unripe mangoes; eggplant, sayote, patola, upo, kundol, sitaw (in pod)'],
            ['Q45_OILS',        '45. OILS AND FATS — Any oil, fats or butter, gata (mantika, star margarine)'],
            ['Q46A_SWEETS',     '46a. SWEETS, BISCUITS, CAKES AND PASTRIES — Chocolates, sweets, candies, pastries, cakes, cookies or biscuits with sweet fillings (Rebisco cream sandwich, Stick-o)'],
            ['Q46B_SSB',        '46b. SUGAR-SWEETENED BEVERAGES (SSB) — Softdrinks, sweet tea, powdered juices like Tang, Eight o\'clock, Nestea, juice drinks like Zest-o, Funchum, Big'],
            ['Q47_SPICES',      '47. SPICES AND CONDIMENTS — Toyo, patis, bagoong, catsup'],
            ['Q48_BEVERAGES',   '48. BEVERAGES — Coffee, tea and the like'],
            ['Q49_SNACKS',      '49. SNACK FOODS — Potato chips, corn snacks like Piattos, Tortillos, Dingdong, Muncher'],
            ['Q50_OTHER_FOOD',  '50. OTHER FOODS — Snails, grubs, insects, etc.'],
            ['Q51_MNP',         '51. MNP — Micronutrient Powder (Nutri-foods MNP, Vita Meena MNP, Vita Nutrient Mix, Micronutrient Growth Mix/MGM)'],
        ];
        foreach ($dds as [$code, $label]) {
            $this->qChoice($survey, ++$o, $code, $label . ' — DDS (0=No, 1=Yes)', 'single_choice', [
                ['1','1 — Yes, consumed yesterday'],
                ['0','0 — No, not consumed'],
            ], false);
        }
        $this->q($survey, ++$o, 'Q52_TOTAL_DDS', '52. TOTAL DDS SCORE', 'number', ['min'=>0,'max'=>19], false);

        // ── VI. ADDITIONAL QUESTIONS ──────────────────────────────────────────
        $q52b_q = $this->qChoice($survey, ++$o, 'Q52_FOOD_USUAL', '52. Was the amount of food your child [NAME] ate yesterday about usual, less than usual, or more than usual?', 'single_choice', [
            ['U','Usual'],['L','Less than usual'],['M','More than usual'],
        ], false);
        $q52a_q = $this->qChoice($survey, ++$o, 'Q52A_LESS_REASON', "52a. What is the main reason why the amount of your child's [NAME] food intake yesterday was LESS than usual?", 'single_choice', [
            ['1','Sickness'],['2','Short of money'],['3','Traveling'],
            ['4','At a social function, a special meal, or on a special day'],
            ['5','Sleeping'],['6','Not hungry'],['7','Other reason (specify in remarks)'],
        ], false);
        $q52b_r = $this->qChoice($survey, ++$o, 'Q52B_MORE_REASON', "52b. What is the main reason why the amount your child's [NAME] food intake yesterday was MORE than usual?", 'single_choice', [
            ['1','Traveling'],['2','At a social function, special meal, or on a special day'],
            ['3','On vacation'],['4','Very hungry'],['5','Some other reason (specify in remarks)'],
        ], false);
        $this->qChoice($survey, ++$o, 'Q53_DIETARY', "53. How would you describe your child's [NAME] current dietary habits?", 'single_choice', [
            ['1','No special diet, she eats almost everything'],
            ['2','Vegetarian'],
            ['3','Special diet (specify in remarks)'],
        ], false);

        // Skip: Q52=Usual → go to Q53; Q52=More → skip 52a, go to 52b
        $q53id = Question::where('survey_id', $survey->id)->where('variable_code', 'Q53_DIETARY')->value('id');
        SkipRule::create(['survey_id'=>$survey->id,'source_question_id'=>$q52b_q->id,'condition_type'=>'equals','condition_value'=>'U','target_question_id'=>$q53id]);
        SkipRule::create(['survey_id'=>$survey->id,'source_question_id'=>$q52b_q->id,'condition_type'=>'equals','condition_value'=>'M','target_question_id'=>$q52b_r->id]);

        // ── VII. INTERVIEW RECORD (Visit 1 and 2) ─────────────────────────────
        foreach (['1','2'] as $v) {
            $this->q($survey, ++$o, "Q54_DATE_V{$v}",        "54. DATE — Visit {$v} (dd-mmm-yyyy)",     'date',      [], false);
            $this->q($survey, ++$o, "Q55_TIME_START_V{$v}",  "55. TIME STARTED — Visit {$v}",           'time',      [], false);
            $this->q($survey, ++$o, "Q56_TIME_END_V{$v}",    "56. TIME ENDED — Visit {$v}",             'time',      [], false);
            $this->qChoice($survey, ++$o, "Q57_STATUS_V{$v}", "57. INTERVIEW STATUS — Visit {$v}", 'single_choice', [
                ['1','1 — Completed'],['2','2 — Partly completed'],['3','3 — Respondent incapacitated'],
                ['4','4 — Refused'],['5','5 — Not at home'],['6','6 — Away (local)'],
                ['7','7 — Away (abroad)'],['8','8 — Other'],
            ], false);
            $this->q($survey, ++$o, "Q57_REMARKS_V{$v}", "57. Remarks — Visit {$v}", 'open_text', [], false);
        }

        // ── VIII. SIGNATURE BLOCK ─────────────────────────────────────────────
        $this->q($survey, ++$o, 'Q58_RESEARCHER',   '58. RESEARCHER (Name and Signature)',   'open_text', [], false);
        $this->q($survey, ++$o, 'Q59_SUPERVISOR',   '59. SUPERVISOR (Name and Signature)',   'open_text', [], false);
        $this->q($survey, ++$o, 'Q60_STUDY_LEADER', '60. STUDY LEADER (Name and Signature)', 'open_text', [], false);

        $qCount = $survey->questions()->count();
        $rCount = SkipRule::where('survey_id', $survey->id)->count();
        $this->command->info("✓ IYCF CU5 survey seeded: {$qCount} questions, {$rCount} skip rules.");
    }

    private function q(Survey $s, int $order, string $code, string $label, string $type, array $config = [], bool $required = true): Question
    {
        return Question::create([
            'survey_id'     => $s->id,
            'variable_code' => $code,
            'label'         => $label,
            'type'          => $type,
            'sort_order'    => $order,
            'is_required'   => $required,
            'config'        => empty($config) ? null : $config,
        ]);
    }

    private function qChoice(Survey $s, int $order, string $code, string $label, string $type, array $options, bool $required = true): Question
    {
        $q = $this->q($s, $order, $code, $label, $type, [], $required);
        foreach ($options as $i => [$optCode, $optLabel]) {
            QuestionOption::create([
                'question_id' => $q->id,
                'option_code' => $optCode,
                'label'       => $optLabel,
                'sort_order'  => $i,
            ]);
        }
        return $q;
    }
}
