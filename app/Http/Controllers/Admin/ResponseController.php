<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\Response;

class ResponseController extends Controller
{
    public function index(Survey $survey)
    {
        $this->authorize($survey);
        $responses = $survey->responses()->latest()->paginate(50);
        return view('admin.responses.index', compact('survey', 'responses'));
    }

    public function show(Survey $survey, Response $response)
    {
        $this->authorize($survey);
        $response->load(['answers.question', 'answers.selectedOptions.questionOption', 'answers.gridCells.gridRow', 'answers.gridCells.questionOption']);
        $questions = $survey->questions()->with(['options', 'gridRows'])->get()->keyBy('id');
        return view('admin.responses.show', compact('survey', 'response', 'questions'));
    }

    public function destroy(Survey $survey, Response $response)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        $response->delete();
        return redirect()->route('admin.surveys.responses.index', $survey)
            ->with('success', 'Response deleted.');
    }

    private function authorize(Survey $survey): void
    {
        if (!auth()->user()->isAdmin() && $survey->user_id !== auth()->id()) abort(403);
    }
}
