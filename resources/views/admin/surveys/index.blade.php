@extends('layouts.admin')
@section('title', 'Surveys')
@section('topbar-actions')
  <a href="{{ route('admin.surveys.create') }}" class="btn btn-primary">+ New Survey</a>
@endsection
@section('content')
<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Title</th><th>Status</th><th>Responses</th><th>Completion</th><th>Owner</th><th>Created</th><th></th></tr></thead>
      <tbody>
      @forelse($surveys as $survey)
        @php
          $total    = $survey->responses_count;
          $complete = $survey->complete_count ?? 0;
          $pct      = $total > 0 ? round($complete / $total * 100) : 0;
        @endphp
        <tr>
          <td><strong>{{ $survey->title }}</strong><br><small style="color:#999">{{ Str::limit($survey->description, 60) }}</small></td>
          <td><span class="badge badge-{{ $survey->status }}">{{ ucfirst($survey->status) }}</span></td>
          <td>{{ $total }}</td>
          <td style="min-width:110px">
            @if($total > 0)
              <div style="font-size:12px;color:#555;margin-bottom:3px">{{ $complete }}/{{ $total }} <span style="color:#888">({{ $pct }}%)</span></div>
              <div style="background:#e8e8e8;border-radius:99px;height:5px;width:90px">
                <div style="background:#155724;height:5px;border-radius:99px;width:{{ $pct }}%"></div>
              </div>
            @else
              <span style="color:#ccc;font-size:12px">—</span>
            @endif
          </td>
          <td>{{ $survey->user?->name ?? '—' }}</td>
          <td>{{ $survey->created_at->format('M d, Y') }}</td>
          <td style="white-space:nowrap">
            <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-secondary btn-sm">View</a>
            <a href="{{ route('admin.surveys.builder', $survey) }}" class="btn btn-primary btn-sm">Builder</a>
          </td>
        </tr>
      @empty
        <tr><td colspan="7" style="text-align:center;color:#888;padding:32px">No surveys found.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="pagination">{{ $surveys->links() }}</div>
</div>
@endsection
