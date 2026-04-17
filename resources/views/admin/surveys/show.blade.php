@extends('layouts.admin')
@section('title', $survey->title)
@section('topbar-actions')
  @if($survey->isOwnedOrAdminBy(auth()->user()))
    <a href="{{ route('admin.surveys.builder', $survey) }}" class="btn btn-primary">Question Builder</a>
    <a href="{{ route('admin.surveys.edit', $survey) }}" class="btn btn-secondary">Edit</a>
  @endif
@endsection
@section('content')
@php
  $isManager = $survey->isOwnedOrAdminBy(auth()->user());
  $total = $survey->responses_count;
  $pct   = $total > 0 ? round($completeCount / $total * 100) : 0;
@endphp

@if(session('success'))<div class="flash flash-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="flash flash-error">{{ session('error') }}</div>@endif

<div class="stats-row">
  <div class="stat-card"><div class="stat-label">Total Responses</div><div class="stat-value">{{ $total }}</div></div>
  <div class="stat-card"><div class="stat-label">Complete</div><div class="stat-value" style="color:#155724">{{ $completeCount }}</div></div>
  <div class="stat-card"><div class="stat-label">Partial</div><div class="stat-value" style="color:#856404">{{ $partialCount }}</div></div>
  <div class="stat-card">
    <div class="stat-label">Completion Rate</div>
    <div class="stat-value" style="font-size:22px;padding-top:4px">@if($total > 0){{ $pct }}%@else—@endif</div>
    @if($total > 0)
      <div style="background:#e8e8e8;border-radius:99px;height:6px;margin-top:8px">
        <div style="background:#155724;height:6px;border-radius:99px;width:{{ $pct }}%;transition:width .4s"></div>
      </div>
      <div style="font-size:11px;color:#aaa;margin-top:4px">{{ $completeCount }} of {{ $total }} complete</div>
    @endif
  </div>
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
    @if($isManager)
      <form action="{{ route('admin.surveys.activate', $survey) }}" method="POST" style="margin-top:12px">
        @csrf <button class="btn btn-primary btn-sm">Activate Survey</button>
      </form>
    @endif
  @endif
</div>

<div class="card">
  <div class="card-header"><span class="card-title">Actions</span></div>
  <div style="display:flex;flex-direction:column;gap:10px">
    <a href="{{ route('admin.surveys.analytics.index', $survey) }}" class="btn btn-gold">View Analytics</a>
    <a href="{{ route('admin.surveys.responses.index', $survey) }}" class="btn btn-secondary">View Responses</a>
    <a href="{{ route('admin.surveys.export.index', $survey) }}" class="btn btn-secondary">Export Data (STG CSV)</a>
    <a href="{{ route('admin.surveys.import.index', $survey) }}" class="btn btn-secondary">Import Responses (CSV)</a>
    @if($isManager)
      @if($survey->status === 'active')
        <form action="{{ route('admin.surveys.close', $survey) }}" method="POST">
          @csrf <button class="btn btn-danger btn-sm">Close Survey</button>
        </form>
      @endif
      <form action="{{ route('admin.surveys.duplicate', $survey) }}" method="POST">
        @csrf <button class="btn btn-secondary btn-sm">Duplicate Survey</button>
      </form>
    @endif
  </div>
</div>
</div>

{{-- Collaborators (owner/admin only) --}}
@if($isManager)
<div class="card" style="margin-top:20px">
  <div class="card-header">
    <span class="card-title">Collaborators</span>
    <span style="font-size:12px;color:#888">Can view & edit responses — cannot access the Question Builder</span>
  </div>

  {{-- Current collaborators --}}
  @php $collaborators = $survey->collaboratorUsers()->get(); @endphp
  @if($collaborators->isNotEmpty())
  <div style="margin-bottom:16px">
    @foreach($collaborators as $collab)
    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f0">
      <div>
        <span style="font-size:13px;font-weight:600">{{ $collab->name }}</span>
        <span style="font-size:12px;color:#888;margin-left:8px">{{ $collab->email }}</span>
        <span class="badge badge-{{ $collab->role }}" style="margin-left:6px">{{ ucfirst($collab->role) }}</span>
      </div>
      <form action="{{ route('admin.surveys.collaborators.destroy', [$survey, $collab]) }}" method="POST">
        @csrf @method('DELETE')
        <button class="btn btn-danger btn-sm" onclick="return confirm('Remove {{ $collab->name }}?')">Remove</button>
      </form>
    </div>
    @endforeach
  </div>
  @else
    <p style="font-size:13px;color:#888;margin-bottom:16px">No collaborators yet.</p>
  @endif

  {{-- Add collaborator --}}
  <form action="{{ route('admin.surveys.collaborators.store', $survey) }}" method="POST" style="display:flex;gap:10px;align-items:flex-end">
    @csrf
    <div class="form-group" style="flex:1;margin:0">
      <label style="font-size:12px;color:#555;margin-bottom:4px;display:block">Add by email address</label>
      <input type="email" name="email" placeholder="researcher@example.com" required
             style="width:100%;padding:8px 10px;border:1px solid #ddd;border-radius:5px;font-size:13px">
    </div>
    <button type="submit" class="btn btn-primary btn-sm" style="white-space:nowrap">Add Collaborator</button>
  </form>
</div>
@endif

@endsection
