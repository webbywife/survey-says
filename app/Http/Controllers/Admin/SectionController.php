<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function store(Request $request, Survey $survey)
    {
        $this->authorize($survey);
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $data['survey_id']  = $survey->id;
        $data['sort_order'] = $survey->sections()->max('sort_order') + 1;
        $section = Section::create($data);
        return response()->json($section);
    }

    public function update(Request $request, Survey $survey, Section $section)
    {
        $this->authorize($survey);
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $section->update($data);
        return response()->json($section);
    }

    public function destroy(Survey $survey, Section $section)
    {
        $this->authorize($survey);
        // Unassign questions from this section before deleting
        $survey->questions()->where('section_id', $section->id)->update(['section_id' => null]);
        $section->delete();
        return response()->json(['ok' => true]);
    }

    private function authorize(Survey $survey): void
    {
        if (!auth()->user()->isAdmin() && $survey->user_id !== auth()->id()) abort(403);
    }
}
