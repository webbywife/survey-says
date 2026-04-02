<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\GridRow;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function store(Request $request, Survey $survey)
    {
        $this->authorizeSurvey($survey);
        $data = $request->validate([
            'variable_code' => [
                'required', 'alpha_dash', 'max:80',
                "unique:questions,variable_code,NULL,id,survey_id,{$survey->id}",
            ],
            'label'      => 'required|string',
            'type'       => 'required|in:single_choice,multi_select,open_text,rating,number,date,time,grid,ph_location',
            'section_id' => 'nullable|exists:sections,id',
            'sort_order' => 'nullable|integer',
            'is_required'=> 'nullable|boolean',
            'help_text'  => 'nullable|string',
            'config'     => 'nullable|array',
        ]);
        $data['survey_id'] = $survey->id;
        $data['sort_order'] = $data['sort_order'] ?? ($survey->questions()->max('sort_order') + 1);
        $question = Question::create($data);
        return response()->json(['question' => $question->load(['options', 'gridRows'])]);
    }

    public function update(Request $request, Survey $survey, Question $question)
    {
        $this->authorizeSurvey($survey);
        $data = $request->validate([
            'variable_code' => [
                'required', 'alpha_dash', 'max:80',
                "unique:questions,variable_code,{$question->id},id,survey_id,{$survey->id}",
            ],
            'label'      => 'required|string',
            'type'       => 'required|in:single_choice,multi_select,open_text,rating,number,date,time,grid,ph_location',
            'section_id' => 'nullable|exists:sections,id',
            'sort_order' => 'nullable|integer',
            'is_required'=> 'nullable|boolean',
            'help_text'  => 'nullable|string',
            'config'     => 'nullable|array',
        ]);
        $question->update($data);
        return response()->json(['question' => $question->load(['options', 'gridRows'])]);
    }

    public function destroy(Survey $survey, Question $question)
    {
        $this->authorizeSurvey($survey);
        $question->delete();
        return response()->json(['ok' => true]);
    }

    public function reorder(Request $request, Survey $survey)
    {
        $this->authorizeSurvey($survey);
        $ids = $request->validate(['ids' => 'required|array'])['ids'];
        foreach ($ids as $i => $id) {
            Question::where('id', $id)->where('survey_id', $survey->id)
                ->update(['sort_order' => $i]);
        }
        return response()->json(['ok' => true]);
    }

    public function storeOption(Request $request, Survey $survey, Question $question)
    {
        $this->authorizeSurvey($survey);
        $data = $request->validate([
            'option_code' => "required|alpha_dash|max:80|unique:question_options,option_code,NULL,id,question_id,{$question->id}",
            'label'       => 'required|string|max:500',
            'sort_order'  => 'nullable|integer',
        ]);
        $data['question_id'] = $question->id;
        $data['sort_order']  = $data['sort_order'] ?? ($question->options()->max('sort_order') + 1);
        return response()->json(QuestionOption::create($data));
    }

    public function updateOption(Request $request, Survey $survey, Question $question, QuestionOption $option)
    {
        $this->authorizeSurvey($survey);
        $data = $request->validate([
            'option_code' => "required|alpha_dash|max:80|unique:question_options,option_code,{$option->id},id,question_id,{$question->id}",
            'label'       => 'required|string|max:500',
        ]);
        $option->update($data);
        return response()->json($option);
    }

    public function destroyOption(Survey $survey, Question $question, QuestionOption $option)
    {
        $this->authorizeSurvey($survey);
        $option->delete();
        return response()->json(['ok' => true]);
    }

    public function storeGridRow(Request $request, Survey $survey, Question $question)
    {
        $this->authorizeSurvey($survey);
        $data = $request->validate([
            'row_code'   => "required|alpha_dash|max:80|unique:grid_rows,row_code,NULL,id,question_id,{$question->id}",
            'label'      => 'required|string|max:500',
            'sort_order' => 'nullable|integer',
        ]);
        $data['question_id'] = $question->id;
        $data['sort_order']  = $data['sort_order'] ?? ($question->gridRows()->max('sort_order') + 1);
        return response()->json(GridRow::create($data));
    }

    public function destroyGridRow(Survey $survey, Question $question, GridRow $gridRow)
    {
        $this->authorizeSurvey($survey);
        $gridRow->delete();
        return response()->json(['ok' => true]);
    }

    private function authorizeSurvey(Survey $survey): void
    {
        if (!auth()->user()->isAdmin() && $survey->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
