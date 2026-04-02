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

class SyncController extends Controller
{
    /**
     * Accept an offline-saved response and persist it to the database.
     * CSRF is intentionally skipped for this endpoint (see bootstrap/app.php).
     * Deduplication is handled via respondent_token.
     */
    public function sync(Request $request, string $token)
    {
        $survey = Survey::where('public_token', $token)->firstOrFail();

        $respondentToken = $request->input('_respondent_token');
        if (!$respondentToken) {
            return response()->json(['error' => 'Missing respondent token'], 422);
        }

        // Deduplicate: if a completed response already exists for this token, skip
        $existing = Response::where('respondent_token', $respondentToken)
            ->where('survey_id', $survey->id)
            ->where('status', 1)
            ->first();

        if ($existing) {
            return response()->json(['ok' => true, 'duplicate' => true, 'serial' => $existing->serial]);
        }

        // Parse the saved_at timestamp from client (UTC ISO string)
        $savedAt = $request->input('_saved_at')
            ? \Carbon\Carbon::parse($request->input('_saved_at'))
            : now();

        // Create the response record
        $response = Response::create([
            'survey_id'        => $survey->id,
            'serial'           => 'TEMP-' . Str::random(8),
            'status'           => 1,
            'started_at'       => $savedAt,
            'completed_at'     => $savedAt,
            'duration_seconds' => (int) $request->input('_duration', 0),
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'respondent_token' => $respondentToken,
            'is_offline_sync'  => true,
        ]);
        $response->generateSerial();

        $questions = $survey->questions()->with(['options', 'gridRows'])->orderBy('sort_order')->get();

        foreach ($questions as $question) {
            $fieldName = 'q_' . $question->id;
            if (!$request->has($fieldName) && !in_array($question->type, ['grid', 'ph_location'])) {
                continue;
            }

            $answer = Answer::create([
                'response_id' => $response->id,
                'question_id' => $question->id,
                'value_text'  => null,
            ]);

            if ($question->type === 'ph_location') {
                $answer->update(['value_text' => json_encode([
                    'province'      => $request->input($fieldName . '_province_name', ''),
                    'city'          => $request->input($fieldName . '_city_name', ''),
                    'barangay'      => $request->input($fieldName . '_barangay', ''),
                    'province_code' => $request->input($fieldName . '_province', ''),
                    'city_code'     => $request->input($fieldName . '_city', ''),
                ])]);

            } elseif ($question->type === 'single_choice') {
                $answer->update(['value_text' => $request->input($fieldName)]);
                $optCode = $request->input($fieldName);
                $opt = $question->options->firstWhere('option_code', $optCode);
                if ($opt) AnswerOption::create(['answer_id' => $answer->id, 'question_option_id' => $opt->id]);

            } elseif ($question->type === 'multi_select') {
                $selected = (array) $request->input($fieldName, []);
                foreach ($selected as $optCode) {
                    $opt = $question->options->firstWhere('option_code', $optCode);
                    if ($opt) AnswerOption::create(['answer_id' => $answer->id, 'question_option_id' => $opt->id]);
                }

            } elseif ($question->type === 'grid') {
                $gridData = $request->input($fieldName, []);
                if (!is_array($gridData)) continue;
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
                    }
                }
            } else {
                $answer->update(['value_text' => $request->input($fieldName)]);
            }
        }

        return response()->json(['ok' => true, 'serial' => $response->serial]);
    }
}
