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
                $values = DB::table('answers')
                    ->where('question_id', $question->id)
                    ->whereNotNull('value_text')
                    ->where('value_text', '!=', '')
                    ->pluck('value_text')
                    ->map(fn($v) => (float) $v)
                    ->sort()
                    ->values();

                if ($values->isEmpty()) continue;

                $n      = $values->count();
                $mid    = intdiv($n, 2);
                $median = $n % 2 === 0
                    ? round(($values[$mid - 1] + $values[$mid]) / 2, 2)
                    : round($values[$mid], 2);

                $charts[] = [
                    'type'   => 'stat',
                    'code'   => $question->variable_code,
                    'label'  => $question->label,
                    'n'      => $n,
                    'avg'    => round($values->average(), 2),
                    'median' => $median,
                    'min'    => round($values->min(), 2),
                    'max'    => round($values->max(), 2),
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
