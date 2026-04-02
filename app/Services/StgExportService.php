<?php
namespace App\Services;

use App\Models\Survey;
use App\Models\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StgExportService
{
    public function __construct(private Survey $survey) {}

    public function buildHeaders(): array
    {
        $fixed = [
            'Serial', 'Status', 'IntDate', 'StartTime', 'EndTime',
            'Duration', 'InterviewerId', 'InterviewerName', 'GPSLat', 'GPSLon',
        ];

        $dynamic = [];
        foreach ($this->survey->questions()->with(['options', 'gridRows'])->get() as $question) {
            if ($question->isMultiSelect()) {
                foreach ($question->options as $option) {
                    $dynamic[] = $question->variable_code . '_' . $option->option_code;
                }
            } elseif ($question->isGrid()) {
                foreach ($question->gridRows as $row) {
                    foreach ($question->options as $col) {
                        $dynamic[] = $question->variable_code . '_' . $row->row_code . '_' . $col->option_code;
                    }
                }
            } elseif ($question->isPHLocation()) {
                $dynamic[] = $question->variable_code;
                $dynamic[] = $question->variable_code . '_CITY';
                $dynamic[] = $question->variable_code . '_BRGY';
            } else {
                $dynamic[] = $question->variable_code;
            }
        }

        return array_merge($fixed, $dynamic);
    }

    public function buildRow(Response $response): array
    {
        $row = [
            $response->serial,
            $response->status,
            $response->started_at?->format('Y-m-d') ?? '',
            $response->started_at?->format('H:i:s') ?? '',
            $response->completed_at?->format('H:i:s') ?? '',
            $response->duration_seconds ?? '',
            '', '', '', '',  // InterviewerId, InterviewerName, GPSLat, GPSLon
        ];

        $answersByQuestion = $response->answers->keyBy('question_id');
        $questions = $this->survey->questions()->with(['options', 'gridRows'])->get();

        foreach ($questions as $question) {
            $answer = $answersByQuestion->get($question->id);

            if ($question->isMultiSelect()) {
                $selectedIds = $answer
                    ? $answer->selectedOptions->pluck('question_option_id')->toArray()
                    : [];
                foreach ($question->options as $option) {
                    $row[] = in_array($option->id, $selectedIds) ? 1 : 0;
                }
            } elseif ($question->type === 'single_choice') {
                if ($answer && $answer->selectedOptions->isNotEmpty()) {
                    $optId = $answer->selectedOptions->first()->question_option_id;
                    $opt   = $question->options->firstWhere('id', $optId);
                    $row[] = $opt ? $opt->option_code : '';
                } else {
                    $row[] = '';
                }
            } elseif ($question->isGrid()) {
                $cellsByRowCol = [];
                if ($answer) {
                    foreach ($answer->gridCells as $cell) {
                        $cellsByRowCol[$cell->grid_row_id . '_' . $cell->question_option_id] = $cell->cell_value;
                    }
                }
                foreach ($question->gridRows as $gridRow) {
                    foreach ($question->options as $col) {
                        $key   = $gridRow->id . '_' . $col->id;
                        $row[] = $cellsByRowCol[$key] ?? '';
                    }
                }
            } elseif ($question->isPHLocation()) {
                $loc = $answer ? json_decode($answer->value_text ?? '{}', true) : [];
                $row[] = $loc['province'] ?? '';
                $row[] = $loc['city']     ?? '';
                $row[] = $loc['barangay'] ?? '';
            } else {
                $row[] = $answer?->value_text ?? '';
            }
        }

        return $row;
    }

    public function download(array $filters): StreamedResponse
    {
        $filename = Str::slug($this->survey->title) . '_export_' . now()->format('Ymd_His');

        $responses = $this->survey->responses()
            ->with([
                'answers.selectedOptions',
                'answers.gridCells',
                'answers.question',
            ])
            ->when($filters['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->when($filters['date_from'] ?? null, fn($q, $d) => $q->whereDate('started_at', '>=', $d))
            ->when($filters['date_to'] ?? null, fn($q, $d) => $q->whereDate('started_at', '<=', $d))
            ->cursor();

        $headers = $this->buildHeaders();

        return response()->streamDownload(function () use ($headers, $responses) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($responses as $response) {
                fputcsv($handle, $this->buildRow($response));
            }
            fclose($handle);
        }, $filename . '.csv', [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ]);
    }
}
