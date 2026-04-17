<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportJob;
use App\Models\Survey;
use App\Services\StgExportService;
use App\Services\StgImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function index(Survey $survey)
    {
        $this->authorize($survey);
        $service = new StgExportService($survey);
        $survey->load(['questions.options', 'questions.gridRows']);
        $headers    = $service->buildHeaders();
        $importJobs = $survey->importJobs()->with('user')->latest()->take(10)->get();
        return view('admin.import.index', compact('survey', 'headers', 'importJobs'));
    }

    public function store(Request $request, Survey $survey)
    {
        $this->authorize($survey);

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        Storage::disk('local')->makeDirectory('imports/tmp');
        $file     = $request->file('csv_file');
        $path     = $file->store('imports/tmp', 'local');
        $fullPath = Storage::disk('local')->path($path);

        $service = new StgImportService($survey);
        $job     = $service->import($fullPath, auth()->id());

        @unlink($fullPath);

        $msg = "Imported {$job->row_count_imported} row(s).";
        if ($job->row_count_skipped > 0) {
            $msg .= " Skipped {$job->row_count_skipped} (duplicates or errors).";
        }

        return redirect()
            ->route('admin.surveys.import.index', $survey)
            ->with('success', $msg)
            ->with('import_job_id', $job->id);
    }

    private function authorize(Survey $survey): void
    {
        if (!auth()->user()->isAdmin() && $survey->user_id !== auth()->id()) abort(403);
    }
}
