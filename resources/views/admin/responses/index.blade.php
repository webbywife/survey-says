@extends('layouts.admin')
@section('title', 'Responses — ' . $survey->title)
@section('topbar-actions')
  <a href="{{ route('admin.surveys.export.index', $survey) }}" class="btn btn-gold btn-sm">Export CSV</a>
  <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-secondary btn-sm">← Survey</a>
@endsection
@section('content')
<div class="card">
  <div class="table-wrap">
    <table>
      @php
        $colCount = 7 + ($nameQuestion ? 1 : 0);
      @endphp
      <thead>
        <tr>
          <th>Serial</th>
          @if($nameQuestion)<th>Name</th>@endif
          <th>Status</th><th>Started</th><th>Completed</th><th>Duration</th><th>IP</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      @forelse($responses as $r)
        @php $nameVal = $nameQuestion ? ($r->answers->first()?->value_text ?? '—') : null; @endphp
        <tr>
          <td><code style="font-size:12px">{{ $r->serial }}</code></td>
          @if($nameQuestion)<td style="font-size:13px">{{ $nameVal }}</td>@endif
          <td><span class="badge badge-{{ $r->status===1?'complete':'partial' }}">{{ $r->status===1?'Complete':'Partial' }}</span></td>
          <td>{{ $r->started_at?->format('M d, Y H:i') ?? '—' }}</td>
          <td>{{ $r->completed_at?->format('H:i') ?? '—' }}</td>
          <td>{{ $r->duration_seconds ? gmdate('i:s', $r->duration_seconds) : '—' }}</td>
          <td style="color:#aaa;font-size:12px">{{ $r->ip_address ?? '—' }}</td>
          <td style="white-space:nowrap">
            <a href="{{ route('admin.surveys.responses.show', [$survey, $r]) }}" class="btn btn-secondary btn-sm">View</a>
            @if(auth()->user()->canEditResponses())
              <a href="{{ route('admin.surveys.responses.edit', [$survey, $r]) }}" class="btn btn-primary btn-sm">Edit</a>
            @endif
            @if(auth()->user()->canCheckResponses())
              <form action="{{ route('admin.surveys.responses.check', [$survey, $r]) }}" method="POST" style="display:inline">
                @csrf
                <button class="btn btn-sm {{ $r->checked_at ? 'btn-primary' : 'btn-secondary' }}"
                  title="{{ $r->checked_at ? 'Checked — click to undo' : 'Mark as checked' }}">
                  ✓ {{ $r->checked_at ? 'Checked' : 'Check' }}
                </button>
              </form>
            @endif
            @if(auth()->user()->canApproveResponses())
              <form action="{{ route('admin.surveys.responses.approve', [$survey, $r]) }}" method="POST" style="display:inline">
                @csrf
                <button class="btn btn-sm {{ $r->approved_at ? 'btn-gold' : 'btn-secondary' }}"
                  title="{{ $r->approved_at ? 'Approved — click to undo' : 'Approve response' }}">
                  ★ {{ $r->approved_at ? 'Approved' : 'Approve' }}
                </button>
              </form>
            @endif
            @if(auth()->user()->isAdmin())
              <form action="{{ route('admin.surveys.responses.destroy', [$survey, $r]) }}" method="POST" style="display:inline">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this response?')">×</button>
              </form>
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="{{ $colCount }}" style="text-align:center;color:#888;padding:40px">No responses yet.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="pagination">{{ $responses->links() }}</div>
</div>
@endsection
