@extends('layouts.admin')
@section('title', 'Surveys')
@section('topbar-actions')
  <a href="{{ route('admin.surveys.create') }}" class="btn btn-primary">+ New Survey</a>
@endsection
@section('content')
<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Title</th><th>Status</th><th>Responses</th><th>Owner</th><th>Created</th><th></th></tr></thead>
      <tbody>
      @forelse($surveys as $survey)
        <tr>
          <td><strong>{{ $survey->title }}</strong><br><small style="color:#999">{{ Str::limit($survey->description, 60) }}</small></td>
          <td><span class="badge badge-{{ $survey->status }}">{{ ucfirst($survey->status) }}</span></td>
          <td>{{ $survey->responses_count }}</td>
          <td>{{ $survey->user?->name ?? '—' }}</td>
          <td>{{ $survey->created_at->format('M d, Y') }}</td>
          <td style="white-space:nowrap">
            <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-secondary btn-sm">View</a>
            <a href="{{ route('admin.surveys.builder', $survey) }}" class="btn btn-primary btn-sm">Builder</a>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" style="text-align:center;color:#888;padding:32px">No surveys found.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="pagination">{{ $surveys->links() }}</div>
</div>
@endsection
