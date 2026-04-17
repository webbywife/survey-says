@extends('layouts.admin')
@section('title', 'Import — ' . $survey->title)
@section('topbar-actions')
  <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-secondary btn-sm">← Survey</a>
@endsection
@section('content')

@if(session('success'))
  <div class="flash flash-success">{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="flash flash-error">
    @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
  </div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

  {{-- Upload Form --}}
  <div class="card">
    <div class="card-header"><span class="card-title">Upload CSV</span></div>

    <div style="background:#fdf6e8;border:1px solid #e2c47a;border-radius:5px;padding:12px;font-size:13px;color:#7a5c00;margin-bottom:20px">
      <strong>Format:</strong> CSV headers must exactly match the column names shown on the right.
      Download a fresh export first to use as a template.
    </div>

    <form action="{{ route('admin.surveys.import.store', $survey) }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="form-group">
        <label>CSV File <span style="color:#dc3545">*</span></label>
        <input type="file" name="csv_file" accept=".csv,text/csv" required style="display:block;width:100%;padding:8px;border:1px solid #ddd;border-radius:5px;font-size:13px">
        <p style="font-size:12px;color:#888;margin-top:4px">Max 10 MB · UTF-8 encoded · First row must be column headers</p>
      </div>

      <div style="background:#f8f8f8;border-radius:5px;padding:12px;font-size:13px;margin-bottom:16px">
        <strong>Rules:</strong>
        <ul style="margin:8px 0 0 18px;line-height:1.8">
          <li>Rows with a <code>Serial</code> that already exists in this survey will be skipped.</li>
          <li>Leave <code>Serial</code> blank to auto-generate.</li>
          <li>Multi-select columns: use <code>1</code> = selected, <code>0</code> / blank = not selected.</li>
          <li>Single-choice columns: use the option code (e.g. <code>M</code>, <code>F</code>).</li>
          <li>Date format: <code>YYYY-MM-DD</code> · Time: <code>HH:MM:SS</code></li>
        </ul>
      </div>

      <button type="submit" class="btn btn-gold">⬆ Import Responses</button>
    </form>
  </div>

  {{-- Column Reference --}}
  <div class="card">
    <div class="card-header">
      <span class="card-title">Required Column Headers</span>
      <span style="font-size:12px;color:#888">{{ count($headers) }} columns</span>
    </div>
    <div style="max-height:420px;overflow-y:auto">
      @foreach($headers as $i => $h)
        <div style="padding:4px 0;border-bottom:1px solid #f8f8f8;font-size:12px;font-family:monospace;display:flex;gap:8px;align-items:center">
          <span style="color:#ccc;width:30px;text-align:right;flex-shrink:0">{{ $i + 1 }}</span>
          <span style="{{ $i < 10 ? 'color:#C9A84C;font-weight:700' : 'color:#444' }}">{{ $h }}</span>
        </div>
      @endforeach
    </div>
  </div>

</div>

{{-- Import History --}}
@if($importJobs->isNotEmpty())
<div class="card" style="margin-top:20px">
  <div class="card-header"><span class="card-title">Import History</span></div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>File</th>
          <th>By</th>
          <th>Total</th>
          <th>Imported</th>
          <th>Skipped</th>
          <th>Status</th>
          <th>Errors</th>
        </tr>
      </thead>
      <tbody>
        @foreach($importJobs as $job)
        <tr>
          <td style="font-size:12px;color:#888">{{ $job->completed_at?->format('Y-m-d H:i') ?? '—' }}</td>
          <td style="font-family:monospace;font-size:12px">{{ $job->original_filename }}</td>
          <td style="font-size:12px">{{ $job->user?->name ?? '—' }}</td>
          <td>{{ $job->row_count_total }}</td>
          <td style="color:#155724;font-weight:600">{{ $job->row_count_imported }}</td>
          <td style="{{ $job->row_count_skipped > 0 ? 'color:#856404;font-weight:600' : '' }}">{{ $job->row_count_skipped }}</td>
          <td>
            <span style="padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;
              background:{{ $job->status === 'completed' ? '#d4edda' : '#f8d7da' }};
              color:{{ $job->status === 'completed' ? '#155724' : '#721c24' }}">
              {{ ucfirst($job->status) }}
            </span>
          </td>
          <td>
            @if($job->error_log)
              <details style="font-size:12px">
                <summary style="cursor:pointer;color:#dc3545">{{ count($job->error_log) }} error(s)</summary>
                <ul style="margin:6px 0 0 16px;line-height:1.6;color:#721c24">
                  @foreach($job->error_log as $err)
                    <li>{{ $err }}</li>
                  @endforeach
                </ul>
              </details>
            @else
              <span style="color:#888">—</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

@endsection
