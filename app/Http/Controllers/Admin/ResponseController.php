<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\AnswerGridCell;
use App\Models\AnswerOption;
use App\Models\Survey;
use App\Models\Response;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function index(Survey $survey)
    {
        $this->authorize($survey);

        // Find the first question whose variable_code or label contains "NAME"
        $nameQuestion = $survey->questions()
            ->where(fn($q) =>
                $q->whereRaw('UPPER(variable_code) LIKE ?', ['%NAME%'])
                  ->orWhereRaw('UPPER(label) LIKE ?', ['%NAME%'])
            )
            ->orderBy('sort_order')
            ->first();

        $locationQuestion = $survey->questions()
            ->where('type', 'ph_location')
            ->orderBy('sort_order')
            ->first();

        $questionIds = array_values(array_filter([$nameQuestion?->id, $locationQuestion?->id]));

        $responses = $survey->responses()
            ->when(!empty($questionIds), fn($q) => $q->with(['answers' => fn($q) => $q->whereIn('question_id', $questionIds)]))
            ->latest()
            ->paginate(50);

        return view('admin.responses.index', compact('survey', 'responses', 'nameQuestion', 'locationQuestion'));
    }

    public function show(Survey $survey, Response $response)
    {
        $this->authorize($survey);
        $response->load(['answers.question', 'answers.selectedOptions.questionOption', 'answers.gridCells.gridRow', 'answers.gridCells.questionOption']);
        $questions = $survey->questions()->with(['options', 'gridRows'])->get()->keyBy('id');
        return view('admin.responses.show', compact('survey', 'response', 'questions'));
    }

    public function edit(Survey $survey, Response $response)
    {
        $this->authorizeEdit($survey);
        $response->load(['answers.question', 'answers.selectedOptions', 'answers.gridCells']);
        $questions = $survey->questions()->with(['options', 'gridRows'])->orderBy('sort_order')->get();
        $answersMap = $response->answers->keyBy('question_id');
        return view('admin.responses.edit', compact('survey', 'response', 'questions', 'answersMap'));
    }

    public function update(Request $request, Survey $survey, Response $response)
    {
        $this->authorizeEdit($survey);

        $questions = $survey->questions()->with(['options', 'gridRows'])->get()->keyBy('id');

        foreach ($questions as $question) {
            $fieldKey = 'q_' . $question->id;

            // Find or create the answer record
            $answer = $response->answers()->firstOrCreate(['question_id' => $question->id]);

            if (in_array($question->type, ['text', 'number', 'date', 'datetime', 'textarea', 'ph_location'])) {
                $answer->value_text = $request->input($fieldKey);
                $answer->save();

            } elseif ($question->type === 'single_choice') {
                $answer->value_text = null;
                $answer->save();
                $answer->selectedOptions()->delete();
                if ($optionId = $request->input($fieldKey)) {
                    AnswerOption::create(['answer_id' => $answer->id, 'question_option_id' => (int) $optionId]);
                }

            } elseif ($question->type === 'multi_select') {
                $answer->value_text = null;
                $answer->save();
                $answer->selectedOptions()->delete();
                foreach ((array) $request->input($fieldKey, []) as $optionId) {
                    AnswerOption::create(['answer_id' => $answer->id, 'question_option_id' => (int) $optionId]);
                }

            } elseif ($question->isGrid()) {
                $answer->value_text = null;
                $answer->save();
                foreach ($question->gridRows as $row) {
                    foreach ($question->options as $col) {
                        $cellKey = 'g_' . $question->id . '_' . $row->id . '_' . $col->id;
                        $cellValue = $request->input($cellKey);
                        AnswerGridCell::updateOrCreate(
                            ['answer_id' => $answer->id, 'grid_row_id' => $row->id, 'question_option_id' => $col->id],
                            ['cell_value' => $cellValue]
                        );
                    }
                }
            }
        }

        return redirect()
            ->route('admin.surveys.responses.show', [$survey, $response])
            ->with('success', 'Response updated successfully.');
    }

    public function destroy(Survey $survey, Response $response)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        $response->delete();
        return redirect()->route('admin.surveys.responses.index', $survey)
            ->with('success', 'Response deleted.');
    }

    public function check(Survey $survey, Response $response)
    {
        $user = auth()->user();
        if (!$user->canCheckResponses()) abort(403);
        if (!$survey->canBeAccessedBy($user)) abort(403);

        if ($response->checked_at) {
            $response->update(['checked_at' => null, 'checked_by' => null]);
            return back()->with('success', 'Check mark removed.');
        }
        $response->update(['checked_at' => now(), 'checked_by' => $user->id]);
        return back()->with('success', 'Response marked as checked.');
    }

    public function approve(Survey $survey, Response $response)
    {
        $user = auth()->user();
        if (!$user->canApproveResponses()) abort(403);
        if (!$survey->canBeAccessedBy($user)) abort(403);

        if ($response->approved_at) {
            $response->update(['approved_at' => null, 'approved_by' => null]);
            return back()->with('success', 'Approval removed.');
        }
        $response->update(['approved_at' => now(), 'approved_by' => $user->id]);
        return back()->with('success', 'Response approved.');
    }

    private function authorize(Survey $survey): void
    {
        if (!$survey->canBeAccessedBy(auth()->user())) abort(403);
    }

    private function authorizeEdit(Survey $survey): void
    {
        $user = auth()->user();
        if (!$user->canEditResponses()) abort(403);
        if (!$survey->canBeAccessedBy($user)) abort(403);
    }
}
