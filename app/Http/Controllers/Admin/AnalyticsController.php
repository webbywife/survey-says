<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Survey $survey)
    {
        $this->authorize($survey);

        $questions = $survey->questions()->with('options')->orderBy('sort_order')->get();
        $totalResponses = $survey->responses()->count();

        // Timeline: responses per day (last 60 days)
        $timeline = $survey->responses()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('count', 'day')
            ->toArray();

        // Per-question analytics
        $charts = [];
        foreach ($questions as $question) {
            if ($question->type === 'single_choice') {
                $counts = DB::table('answers')
                    ->join('answer_options', 'answers.id', '=', 'answer_options.answer_id')
                    ->join('question_options', 'answer_options.question_option_id', '=', 'question_options.id')
                    ->where('answers.question_id', $question->id)
                    ->selectRaw('question_options.label as label, question_options.option_code as code, COUNT(*) as count')
                    ->groupBy('question_options.id', 'label', 'code')
                    ->orderByDesc('count')
                    ->get();

                if ($counts->isEmpty()) continue;

                $charts[] = [
                    'type'     => 'bar',
                    'code'     => $question->variable_code,
                    'label'    => $question->label,
                    'labels'   => $counts->pluck('label')->toArray(),
                    'data'     => $counts->pluck('count')->toArray(),
                    'total'    => $counts->sum('count'),
                ];

            } elseif ($question->type === 'multi_select') {
                $counts = DB::table('answers')
                    ->join('answer_options', 'answers.id', '=', 'answer_options.answer_id')
                    ->join('question_options', 'answer_options.question_option_id', '=', 'question_options.id')
                    ->where('answers.question_id', $question->id)
                    ->selectRaw('question_options.label as label, COUNT(*) as count')
                    ->groupBy('question_options.id', 'label')
                    ->orderByDesc('count')
                    ->get();

                if ($counts->isEmpty()) continue;

                $charts[] = [
                    'type'     => 'bar',
                    'code'     => $question->variable_code,
                    'label'    => $question->label,
                    'labels'   => $counts->pluck('label')->toArray(),
                    'data'     => $counts->pluck('count')->toArray(),
                    'total'    => $counts->sum('count'),
                ];

            } elseif ($question->type === 'number') {
                $stats = DB::table('answers')
                    ->where('question_id', $question->id)
                    ->whereNotNull('value_text')
                    ->where('value_text', '!=', '')
                    ->selectRaw('COUNT(*) as n, AVG(CAST(value_text AS DECIMAL(10,4))) as avg, MIN(CAST(value_text AS DECIMAL(10,4))) as min, MAX(CAST(value_text AS DECIMAL(10,4))) as max')
                    ->first();

                if (!$stats || $stats->n == 0) continue;

                $charts[] = [
                    'type'  => 'stat',
                    'code'  => $question->variable_code,
                    'label' => $question->label,
                    'n'     => $stats->n,
                    'avg'   => round($stats->avg, 2),
                    'min'   => round($stats->min, 2),
                    'max'   => round($stats->max, 2),
                ];
            }
        }

        return view('admin.analytics.index', compact('survey', 'totalResponses', 'timeline', 'charts'));
    }

    private function authorize(Survey $survey): void
    {
        if (!auth()->user()->isAdmin() && $survey->user_id !== auth()->id()) abort(403);
    }
}
