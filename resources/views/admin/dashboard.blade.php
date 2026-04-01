@extends('layouts.admin')
@section('title', 'Dashboard')
@section('topbar-actions')
  <a href="{{ route('admin.surveys.create') }}" class="btn btn-primary btn-sm">+ New Survey</a>
@endsection
@section('content')
<div class="stats-row">
  <div class="stat-card">
    <div class="stat-label">Total Surveys</div>
    <div class="stat-value">{{ $totalSurveys }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Active</div>
    <div class="stat-value" style="color:#155724">{{ $activeSurveys }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Total Responses</div>
    <div class="stat-value">{{ $totalResponses }}</div>
  </div>
  @if($totalUsers !== null)
  <div class="stat-card">
    <div class="stat-label">Users</div>
    <div class="stat-value">{{ $totalUsers }}</div>
  </div>
  @endif
</div>

<div class="card">
  <div class="card-header">
    <span class="card-title">Recent Surveys</span>
    <a href="{{ route('admin.surveys.index') }}" class="btn btn-secondary btn-sm">View All</a>
  </div>
  @if($recentSurveys->isEmpty())
    <p style="color:#888;font-size:14px">No surveys yet. <a href="{{ route('admin.surveys.create') }}" style="color:var(--maroon)">Create your first survey →</a></p>
  @else
  <div class="table-wrap">
    <table>
      <thead><tr><th>Title</th><th>Status</th><th>Owner</th><th>Created</th><th></th></tr></thead>
      <tbody>
      @foreach($recentSurveys as $survey)
        <tr>
          <td><strong>{{ $survey->title }}</strong></td>
          <td><span class="badge badge-{{ $survey->status }}">{{ ucfirst($survey->status) }}</span></td>
          <td>{{ $survey->user?->name ?? '—' }}</td>
          <td>{{ $survey->created_at->format('M d, Y') }}</td>
          <td><a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-secondary btn-sm">View</a></td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection
