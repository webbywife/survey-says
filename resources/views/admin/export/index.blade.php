@extends('layouts.admin')
@section('title', 'Export — ' . $survey->title)
@section('topbar-actions')
  <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-secondary btn-sm">← Survey</a>
@endsection
@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">
<div class="card">
  <div class="card-header"><span class="card-title">Export Settings</span></div>
  <form action="{{ route('admin.surveys.export.download', $survey) }}" method="GET">
    <div class="form-group">
      <label>Status Filter</label>
      <select name="status">
        <option value="">All responses ({{ $responseCount }})</option>
        <option value="1">Complete only</option>
        <option value="2">Partial only</option>
      </select>
    </div>
    <div class="form-row">
      <div class="form-group"><label>From Date</label><input type="date" name="date_from"></div>
      <div class="form-group"><label>To Date</label><input type="date" name="date_to"></div>
    </div>
    <div class="form-group">
      <label>Export Format</label>
      <div style="display:flex;gap:12px;margin-top:4px">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-weight:normal">
          <input type="radio" name="format" value="csv" checked>
          <span>CSV <span style="color:#888;font-size:12px">(.csv)</span></span>
        </label>
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-weight:normal">
          <input type="radio" name="format" value="xlsx">
          <span>Excel <span style="color:#888;font-size:12px">(.xlsx)</span></span>
        </label>
      </div>
    </div>
    <div style="background:#fdf6e8;border:1px solid #e2c47a;border-radius:5px;padding:12px;font-size:13px;color:#7a5c00;margin-bottom:16px">
      <strong>STG Compatible:</strong> This export matches Survey to Go's format. Variable codes in column headers align with your STG project for direct merge.
    </div>
    <button type="submit" class="btn btn-gold">⬇ Download Export</button>
  </form>
</div>

<div class="card">
  <div class="card-header">
    <span class="card-title">Export Columns Preview</span>
    <span style="font-size:12px;color:#888">{{ count($headers) }} columns</span>
  </div>
  <div style="max-height:400px;overflow-y:auto">
  @foreach($headers as $i => $h)
    <div style="padding:4px 0;border-bottom:1px solid #f8f8f8;font-size:12px;font-family:monospace;display:flex;gap:8px;align-items:center">
      <span style="color:#ccc;width:30px;text-align:right;flex-shrink:0">{{ $i+1 }}</span>
      <span style="{{ $i < 10 ? 'color:#C9A84C;font-weight:700' : 'color:#444' }}">{{ $h }}</span>
    </div>
  @endforeach
  </div>
</div>
</div>
@endsection
