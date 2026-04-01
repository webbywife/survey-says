@extends('layouts.admin')
@section('title', $survey->title)
@section('topbar-actions')
  <a href="{{ route('admin.surveys.builder', $survey) }}" class="btn btn-primary">Question Builder</a>
  <a href="{{ route('admin.surveys.edit', $survey) }}" class="btn btn-secondary">Edit</a>
@endsection
@section('content')
<div class="stats-row">
  <div class="stat-card"><div class="stat-label">Total Responses</div><div class="stat-value">{{ $survey->responses_count }}</div></div>
  <div class="stat-card"><div class="stat-label">Complete</div><div class="stat-value" style="color:#155724">{{ $completeCount }}</div></div>
  <div class="stat-card"><div class="stat-label">Partial</div><div class="stat-value" style="color:#856404">{{ $partialCount }}</div></div>
  <div class="stat-card"><div class="stat-label">Status</div><div class="stat-value" style="font-size:16px;padding-top:6px"><span class="badge badge-{{ $survey->status }}">{{ ucfirst($survey->status) }}</span></div></div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
<div class="card">
  <div class="card-header"><span class="card-title">Public Survey Link</span></div>
  @if($survey->status === 'active')
    <div style="background:#f8f8f8;border:1px solid #eee;border-radius:5px;padding:12px;font-size:13px;word-break:break-all;margin-bottom:12px">
      {{ url('/s/' . $survey->public_token) }}
    </div>
    <a href="{{ url('/s/' . $survey->public_token) }}" target="_blank" class="btn btn-gold btn-sm">Open Survey ↗</a>
  @else
    <p style="color:#888;font-size:13px">Activate the survey to get the public link.</p>
    <form action="{{ route('admin.surveys.activate', $survey) }}" method="POST" style="margin-top:12px">
      @csrf <button class="btn btn-primary btn-sm">Activate Survey</button>
    </form>
  @endif
</div>

<div class="card">
  <div class="card-header"><span class="card-title">Actions</span></div>
  <div style="display:flex;flex-direction:column;gap:10px">
    <a href="{{ route('admin.surveys.responses.index', $survey) }}" class="btn btn-secondary">View Responses</a>
    <a href="{{ route('admin.surveys.export.index', $survey) }}" class="btn btn-secondary">Export Data (STG CSV)</a>
    @if($survey->status === 'active')
      <form action="{{ route('admin.surveys.close', $survey) }}" method="POST">
        @csrf <button class="btn btn-danger btn-sm">Close Survey</button>
      </form>
    @endif
    <form action="{{ route('admin.surveys.duplicate', $survey) }}" method="POST">
      @csrf <button class="btn btn-secondary btn-sm">Duplicate Survey</button>
    </form>
  </div>
</div>
</div>
@endsection
