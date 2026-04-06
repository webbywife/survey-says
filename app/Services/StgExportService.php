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
            '', '', '', '',
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

    public function download(array $filters, string $format = 'csv'): StreamedResponse
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
            ->when($filters['date_to'] ?? null, fn($q, $d) => $q->whereDate('started_at', '<=', $d));

        if ($format === 'xlsx') {
            return $this->downloadXlsx($filename, $responses->get());
        }

        return $this->downloadCsv($filename, $responses->cursor());
    }

    private function downloadCsv(string $filename, iterable $responses): StreamedResponse
    {
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

    private function downloadXlsx(string $filename, iterable $responses): StreamedResponse
    {
        $headers = $this->buildHeaders();
        $rows = [$headers];
        foreach ($responses as $response) {
            $rows[] = $this->buildRow($response);
        }

        $xlsx = $this->buildXlsx($rows);

        return response()->streamDownload(function () use ($xlsx) {
            echo $xlsx;
        }, $filename . '.xlsx', [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.xlsx"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    // Builds a valid .xlsx file using only ZipArchive (no external dependencies)
    private function buildXlsx(array $rows): string
    {
        $sharedStrings = [];
        $sharedIndex   = [];

        $getStringIndex = function (string $value) use (&$sharedStrings, &$sharedIndex): int {
            if (!isset($sharedIndex[$value])) {
                $sharedIndex[$value] = count($sharedStrings);
                $sharedStrings[]     = $value;
            }
            return $sharedIndex[$value];
        };

        $colLetter = function (int $n): string {
            $letter = '';
            while ($n >= 0) {
                $letter = chr($n % 26 + 65) . $letter;
                $n      = intdiv($n, 26) - 1;
            }
            return $letter;
        };

        // Build sheet XML
        $sheetXml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $sheetXml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        $sheetXml .= '<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>';
        $sheetXml .= '<sheetData>';

        foreach ($rows as $rowIndex => $row) {
            $rowNum    = $rowIndex + 1;
            $sheetXml .= '<row r="' . $rowNum . '">';
            foreach ($row as $colIndex => $value) {
                $cellRef = $colLetter($colIndex) . $rowNum;
                $value   = (string) $value;
                if (is_numeric($value) && $value !== '') {
                    $sheetXml .= '<c r="' . $cellRef . '" t="n"><v>' . htmlspecialchars($value, ENT_XML1) . '</v></c>';
                } else {
                    $si        = $getStringIndex($value);
                    $sheetXml .= '<c r="' . $cellRef . '" t="s"><v>' . $si . '</v></c>';
                }
            }
            $sheetXml .= '</row>';
        }

        $sheetXml .= '</sheetData>';
        // Bold + gold header row style applied via styles.xml
        $sheetXml .= '</worksheet>';

        // Shared strings XML
        $ssXml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $ssXml .= '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($sharedStrings) . '" uniqueCount="' . count($sharedStrings) . '">';
        foreach ($sharedStrings as $s) {
            $ssXml .= '<si><t xml:space="preserve">' . htmlspecialchars($s, ENT_XML1) . '</t></si>';
        }
        $ssXml .= '</sst>';

        // Styles XML (bold header in row 1)
        $stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="2">
    <font><sz val="11"/><name val="Calibri"/></font>
    <font><b/><sz val="11"/><name val="Calibri"/></font>
  </fonts>
  <fills count="3">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FFC9A84C"/></patternFill></fill>
  </fills>
  <borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>
  <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
  <cellXfs count="2">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/>
  </cellXfs>
</styleSheet>';

        // Workbook XML
        $workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
          xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets><sheet name="Responses" sheetId="1" r:id="rId1"/></sheets>
</workbook>';

        // Relationships
        $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';

        $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';

        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';

        // Write to temp file as ZipArchive
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');
        $zip = new \ZipArchive();
        $zip->open($tmp, \ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml',         $contentTypes);
        $zip->addFromString('_rels/.rels',                 $rootRels);
        $zip->addFromString('xl/workbook.xml',             $workbookXml);
        $zip->addFromString('xl/_rels/workbook.xml.rels',  $workbookRels);
        $zip->addFromString('xl/worksheets/sheet1.xml',    $sheetXml);
        $zip->addFromString('xl/sharedStrings.xml',        $ssXml);
        $zip->addFromString('xl/styles.xml',               $stylesXml);
        $zip->close();

        $contents = file_get_contents($tmp);
        unlink($tmp);
        return $contents;
    }
}
