<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $query = $user->isAdmin() ? Survey::withTrashed() : Survey::ownedBy($user->id);
        $surveys = $query
            ->withCount('responses')
            ->withCount(['responses as complete_count' => fn($q) => $q->where('status', 1)])
            ->with('user')->latest()->paginate(20);
        return view('admin.surveys.index', compact('surveys'));
    }

    public function create()
    {
        return view('admin.surveys.form', ['survey' => new Survey]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['user_id'] = auth()->id();
        $survey = Survey::create($data);
        return redirect()->route('admin.surveys.builder', $survey)
            ->with('success', 'Survey created. Now add your questions.');
    }

    public function show(Survey $survey)
    {
        $this->authorize($survey);
        $survey->loadCount('responses');
        $completeCount = $survey->responses()->where('status', 1)->count();
        $partialCount  = $survey->responses()->where('status', 2)->count();
        return view('admin.surveys.show', compact('survey', 'completeCount', 'partialCount'));
    }

    public function edit(Survey $survey)
    {
        $this->authorize($survey);
        return view('admin.surveys.form', compact('survey'));
    }

    public function update(Request $request, Survey $survey)
    {
        $this->authorize($survey);
        $survey->update($this->validated($request, $survey->id));
        return back()->with('success', 'Survey updated.');
    }

    public function destroy(Survey $survey)
    {
        $this->authorize($survey);
        $survey->delete();
        return redirect()->route('admin.surveys.index')->with('success', 'Survey deleted.');
    }

    public function builder(Survey $survey)
    {
        $this->authorize($survey);
        $survey->load([
            'sections' => fn($q) => $q->orderBy('sort_order'),
            'questions.options',
            'questions.gridRows',
            'skipRules',
        ]);

        // Sort questions: by section sort_order first, then question sort_order
        $sectionOrder = $survey->sections->pluck('sort_order', 'id');
        $sorted = $survey->questions->sortBy(function ($q) use ($sectionOrder) {
            $sec = $q->section_id ? ($sectionOrder[$q->section_id] ?? 9999) : 9999;
            return sprintf('%05d_%05d', $sec, $q->sort_order);
        })->values();
        $survey->setRelation('questions', $sorted);

        return view('admin.surveys.builder', compact('survey'));
    }

    public function activate(Survey $survey)
    {
        $this->authorize($survey);
        $survey->update(['status' => 'active']);
        return back()->with('success', 'Survey is now active.');
    }

    public function close(Survey $survey)
    {
        $this->authorize($survey);
        $survey->update(['status' => 'closed']);
        return back()->with('success', 'Survey closed.');
    }

    public function duplicate(Survey $survey)
    {
        $this->authorize($survey);
        $new = $survey->replicate(['deleted_at']);
        $new->title       = $survey->title . ' (Copy)';
        $new->status      = 'draft';
        $new->public_token = Str::random(32);
        $new->user_id     = auth()->id();
        $new->save();

        foreach ($survey->questions()->with(['options', 'gridRows'])->get() as $q) {
            $newQ = $q->replicate();
            $newQ->survey_id = $new->id;
            $newQ->save();
            foreach ($q->options as $opt) {
                $newOpt = $opt->replicate();
                $newOpt->question_id = $newQ->id;
                $newOpt->save();
            }
            foreach ($q->gridRows as $row) {
                $newRow = $row->replicate();
                $newRow->question_id = $newQ->id;
                $newRow->save();
            }
        }

        return redirect()->route('admin.surveys.builder', $new)
            ->with('success', 'Survey duplicated.');
    }

    private function authorize(Survey $survey): void
    {
        if (!auth()->user()->isAdmin() && $survey->user_id !== auth()->id()) {
            abort(403);
        }
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'status'             => 'required|in:draft,active,closed',
            'starts_at'          => 'nullable|date',
            'ends_at'            => 'nullable|date|after_or_equal:starts_at',
            'allow_partial_save' => 'nullable|boolean',
            'show_progress_bar'  => 'nullable|boolean',
            'logo_file'          => 'nullable|image|max:2048',
            'logo_url'           => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('logo_file')) {
            $path = $request->file('logo_file')->store('logos', 'public');
            $data['logo_url'] = '/storage/' . $path;
        }
        unset($data['logo_file']);

        return $data;
    }
}
