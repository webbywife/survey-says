<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Services\StgExportService;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function index(Survey $survey)
    {
        $this->authorize($survey);
        $service = new StgExportService($survey);
        $survey->load(['questions.options', 'questions.gridRows']);
        $headers = $service->buildHeaders();
        $responseCount = $survey->responses()->count();
        return view('admin.export.index', compact('survey', 'headers', 'responseCount'));
    }

    public function download(Request $request, Survey $survey)
    {
        $this->authorize($survey);
        $filters = $request->validate([
            'status'    => 'nullable|in:1,2',
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date',
            'format'    => 'nullable|in:csv,xlsx',
        ]);
        $format = $filters['format'] ?? 'csv';
        $service = new StgExportService($survey);
        return $service->download($filters, $format);
    }

    private function authorize(Survey $survey): void
    {
        if (!auth()->user()->isAdmin() && $survey->user_id !== auth()->id()) abort(403);
    }
}
