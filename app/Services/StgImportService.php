<?php
namespace App\Services;

use App\Models\Answer;
use App\Models\AnswerGridCell;
use App\Models\AnswerOption;
use App\Models\ImportJob;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use Illuminate\Support\Carbon;

class StgImportService
{
    private array $questions;
    private array $headerMap = [];   // column_name => meta about the question
    private array $errors    = [];
    private int   $imported  = 0;
    private int   $skipped   = 0;

    public function __construct(private Survey $survey) {}

    public function import(string $csvPath, int $userId): ImportJob
    {
        $this->buildHeaderMap();

        $handle    = fopen($csvPath, 'r');
        $headers   = fgetcsv($handle);
        $headers   = array_map('trim', $headers);
        $rowNumber = 1;
        $total     = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count(array_filter($row)) === 0) continue;
            $total++;
            $data = array_combine($headers, array_pad($row, count($headers), ''));
            $this->processRow($data, $rowNumber);
        }

        fclose($handle);

        return ImportJob::create([
            'survey_id'            => $this->survey->id,
            'user_id'              => $userId,
            'original_filename'    => basename($csvPath),
            'row_count_total'      => $total,
            'row_count_imported'   => $this->imported,
            'row_count_skipped'    => $this->skipped,
            'status'               => 'done',
            'error_log'            => $this->errors ?: null,
            'completed_at'         => now(),
        ]);
    }

    private function processRow(array $data, int $rowNumber): void
    {
        $serial = trim($data['Serial'] ?? '');

        if ($serial && Response::where('serial', $serial)->where('survey_id', $this->survey->id)->exists()) {
            $this->skipped++;
            return;
        }

        try {
            $intDate   = trim($data['IntDate']    ?? '');
            $startTime = trim($data['StartTime']  ?? '');
            $endTime   = trim($data['EndTime']    ?? '');
            $duration  = trim($data['Duration']   ?? '');

            $startedAt   = $this->parseDateTime($intDate, $startTime);
            $completedAt = $this->parseDateTime($intDate, $endTime);

            $response = Response::create([
                'survey_id'        => $this->survey->id,
                'serial'           => $serial ?: null,
                'status'           => (int) ($data['Status'] ?? 1),
                'started_at'       => $startedAt,
                'completed_at'     => $completedAt,
                'duration_seconds' => is_numeric($duration) ? (int) $duration : null,
                'is_offline_sync'  => 0,
            ]);

            if (!$serial) {
                $response->generateSerial();
            }

            foreach ($this->questions as $question) {
                $this->writeAnswer($response, $question, $data);
            }

            $this->imported++;
        } catch (\Throwable $e) {
            $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
            $this->skipped++;
        }
    }

    private function writeAnswer(Response $response, Question $question, array $data): void
    {
        $code = $question->variable_code;

        if ($question->isMultiSelect()) {
            $selectedOptionIds = [];
            foreach ($question->options as $option) {
                $col = $code . '_' . $option->option_code;
                if (isset($data[$col]) && trim($data[$col]) === '1') {
                    $selectedOptionIds[] = $option->id;
                }
            }
            if (empty($selectedOptionIds)) return;

            $answer = Answer::create(['response_id' => $response->id, 'question_id' => $question->id]);
            foreach ($selectedOptionIds as $optId) {
                AnswerOption::create(['answer_id' => $answer->id, 'question_option_id' => $optId]);
            }

        } elseif ($question->type === 'single_choice') {
            $value = trim($data[$code] ?? '');
            if ($value === '') return;

            $option = $question->options->firstWhere('option_code', $value);
            if (!$option) return;

            $answer = Answer::create(['response_id' => $response->id, 'question_id' => $question->id]);
            AnswerOption::create(['answer_id' => $answer->id, 'question_option_id' => $option->id]);

        } elseif ($question->isGrid()) {
            $hasCells = false;
            $answer   = null;

            foreach ($question->gridRows as $gridRow) {
                foreach ($question->options as $col) {
                    $colName = $code . '_' . $gridRow->row_code . '_' . $col->option_code;
                    $value   = trim($data[$colName] ?? '');
                    if ($value === '') continue;

                    if (!$answer) {
                        $answer = Answer::create(['response_id' => $response->id, 'question_id' => $question->id]);
                    }
                    AnswerGridCell::create([
                        'answer_id'          => $answer->id,
                        'grid_row_id'        => $gridRow->id,
                        'question_option_id' => $col->id,
                        'cell_value'         => $value,
                    ]);
                    $hasCells = true;
                }
            }

        } elseif ($question->isPHLocation()) {
            $province = trim($data[$code]            ?? '');
            $city     = trim($data[$code . '_CITY']  ?? '');
            $barangay = trim($data[$code . '_BRGY']  ?? '');

            if ($province === '' && $city === '' && $barangay === '') return;

            Answer::create([
                'response_id' => $response->id,
                'question_id' => $question->id,
                'value_text'  => json_encode(compact('province', 'city', 'barangay')),
            ]);

        } else {
            $value = trim($data[$code] ?? '');
            if ($value === '') return;

            Answer::create([
                'response_id' => $response->id,
                'question_id' => $question->id,
                'value_text'  => $value,
            ]);
        }
    }

    private function buildHeaderMap(): void
    {
        $this->questions = $this->survey
            ->questions()
            ->with(['options', 'gridRows'])
            ->get()
            ->all();
    }

    private function parseDateTime(string $date, string $time): ?Carbon
    {
        if ($date === '') return null;
        try {
            return Carbon::parse($time !== '' ? "$date $time" : $date);
        } catch (\Throwable) {
            return null;
        }
    }

    public function getErrors(): array  { return $this->errors; }
    public function getImported(): int  { return $this->imported; }
    public function getSkipped(): int   { return $this->skipped; }
}
