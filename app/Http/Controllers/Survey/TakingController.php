<?php
namespace App\Http\Controllers\Survey;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\Response;
use App\Models\Answer;
use App\Models\AnswerOption;
use App\Models\AnswerGridCell;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TakingController extends Controller
{
    public function show(string $token)
    {
        $survey = Survey::where('public_token', $token)->firstOrFail();

        if (!$survey->isOpen()) {
            return view('survey.closed', compact('survey'));
        }

        $survey->load([
            'sections.questions.options',
            'sections.questions.gridRows',
            'questions.options',
            'questions.gridRows',
            'skipRules',
        ]);

        // Resume partial or start new
        $respondentToken = session('respondent_token_' . $survey->id);
        $response = null;
        $existingAnswers = [];

        if ($respondentToken) {
            $response = Response::where('respondent_token', $respondentToken)
                ->where('survey_id', $survey->id)
                ->where('status', 2)
                ->with('answers.selectedOptions', 'answers.gridCells')
                ->first();
            if ($response) {
                foreach ($response->answers as $ans) {
                    $existingAnswers[$ans->question_id] = $ans;
                }
            }
        }

        $questions = $survey->questions()->with(['options', 'gridRows'])->orderBy('sort_order')->get();
        $skipRulesJson = $survey->skipRules->map(fn($r) => [
            'source' => $r->source_question_id,
            'type'   => $r->condition_type,
            'value'  => $r->condition_value,
            'target' => $r->target_question_id,
        ])->values()->toJson();

        return view('survey.take', compact('survey', 'questions', 'response', 'existingAnswers', 'skipRulesJson'));
    }

    public function submit(Request $request, string $token)
    {
        $survey = Survey::where('public_token', $token)->firstOrFail();
        if (!$survey->isOpen()) abort(403, 'Survey is closed.');

        // Get or create response
        $respondentToken = session('respondent_token_' . $survey->id);
        $response = null;

        if ($respondentToken) {
            $response = Response::where('respondent_token', $respondentToken)
                ->where('survey_id', $survey->id)
                ->where('status', 2)
                ->first();
        }

        if (!$response) {
            $respondentToken = Str::random(32);
            session(['respondent_token_' . $survey->id => $respondentToken]);
            $response = Response::create([
                'survey_id'       => $survey->id,
                'serial'          => 'TEMP-' . Str::random(8),
                'status'          => 2,
                'started_at'      => now(),
                'ip_address'      => $request->ip(),
                'user_agent'      => $request->userAgent(),
                'respondent_token'=> $respondentToken,
            ]);
            $response->generateSerial();
        }

        $questions = $survey->questions()->with(['options', 'gridRows'])->orderBy('sort_order')->get();

        // Save all submitted answers
        foreach ($questions as $question) {
            $fieldName = 'q_' . $question->id;
            if (!$request->has($fieldName) && !in_array($question->type, ['grid', 'ph_location'])) continue;

            $answer = Answer::updateOrCreate(
                ['response_id' => $response->id, 'question_id' => $question->id],
                ['value_text' => null]
            );

            if ($question->type === 'ph_location') {
                $answer->update(['value_text' => json_encode([
                    'province' => $request->input($fieldName . '_province_name', ''),
                    'city'     => $request->input($fieldName . '_city_name', ''),
                    'barangay' => $request->input($fieldName . '_barangay', ''),
                    'province_code' => $request->input($fieldName . '_province', ''),
                    'city_code'     => $request->input($fieldName . '_city', ''),
                ])]);

            } elseif ($question->type === 'single_choice') {
                $answer->update(['value_text' => $request->input($fieldName)]);
                $answer->selectedOptions()->delete();
                $optCode = $request->input($fieldName);
                $opt = $question->options->firstWhere('option_code', $optCode);
                if ($opt) AnswerOption::create(['answer_id' => $answer->id, 'question_option_id' => $opt->id]);

            } elseif ($question->type === 'multi_select') {
                $selected = (array)$request->input($fieldName, []);
                $answer->selectedOptions()->delete();
                foreach ($selected as $optCode) {
                    $opt = $question->options->firstWhere('option_code', $optCode);
                    if ($opt) AnswerOption::create(['answer_id' => $answer->id, 'question_option_id' => $opt->id]);
                }

            } elseif ($question->type === 'grid') {
                $answer->gridCells()->delete();
                $gridData = $request->input($fieldName, []);
                foreach ($gridData as $rowCode => $colValues) {
                    $gridRow = $question->gridRows->firstWhere('row_code', $rowCode);
                    if (!$gridRow) continue;
                    if (is_array($colValues)) {
                        foreach ($colValues as $colCode => $val) {
                            $col = $question->options->firstWhere('option_code', $colCode);
                            if (!$col) continue;
                            AnswerGridCell::create([
                                'answer_id'          => $answer->id,
                                'grid_row_id'        => $gridRow->id,
                                'question_option_id' => $col->id,
                                'cell_value'         => $val,
                            ]);
                        }
                    } else {
                        AnswerGridCell::create([
                            'answer_id'   => $answer->id,
                            'grid_row_id' => $gridRow->id,
                            'question_option_id' => null,
                            'cell_value'  => $colValues,
                        ]);
                    }
                }
            } else {
                $answer->update(['value_text' => $request->input($fieldName)]);
            }
        }

        // Mark complete
        $response->update([
            'status'           => 1,
            'completed_at'     => now(),
            'duration_seconds' => now()->diffInSeconds($response->started_at),
        ]);

        session()->forget('respondent_token_' . $survey->id);

        return redirect()->route('survey.complete', $token);
    }

    public function complete(string $token)
    {
        $survey = Survey::where('public_token', $token)->firstOrFail();
        return view('survey.complete', compact('survey'));
    }
}
