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
        $sortUrl = fn($col) => request()->fullUrlWithQuery([
            'sort' => $col,
            'dir'  => ($sort === $col && $dir === 'asc') ? 'desc' : 'asc',
        ]);
        $sortIcon = fn($col) => $sort === $col
            ? ($dir === 'asc' ? ' ↑' : ' ↓')
            : ' ↕';
      @endphp
      <thead>
        <tr>
          <th><a href="{{ $sortUrl('serial') }}" style="color:inherit;text-decoration:none;white-space:nowrap">Serial{!! $sortIcon('serial') !!}</a></th>
          @if($nameQuestion)<th>Name</th>@endif
          <th><a href="{{ $sortUrl('status') }}" style="color:inherit;text-decoration:none;white-space:nowrap">Status{!! $sortIcon('status') !!}</a></th>
          <th><a href="{{ $sortUrl('started_at') }}" style="color:inherit;text-decoration:none;white-space:nowrap">Started{!! $sortIcon('started_at') !!}</a></th>
          <th><a href="{{ $sortUrl('completed_at') }}" style="color:inherit;text-decoration:none;white-space:nowrap">Completed{!! $sortIcon('completed_at') !!}</a></th>
          <th>City / Municipality</th><th>Barangay</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      @forelse($responses as $r)
        @php
          $nameVal = $nameQuestion ? ($r->answers->where('question_id', $nameQuestion->id)->first()?->value_text ?? '—') : null;
          $locAns  = $locationQuestion ? $r->answers->where('question_id', $locationQuestion->id)->first() : null;
          $locRaw  = $locAns ? json_decode($locAns->value_text ?? '{}', true) : null;
          $cityVal = $locRaw['city'] ?? $locAns?->value_text ?? '—';
          $brgyVal = $barangayQuestion
              ? ($r->answers->where('question_id', $barangayQuestion->id)->first()?->value_text ?? '—')
              : ($locRaw['barangay'] ?? '—');
        @endphp
        <tr>
          <td><code style="font-size:12px">{{ $r->serial }}</code></td>
          @if($nameQuestion)<td style="font-size:13px">{{ $nameVal }}</td>@endif
          <td><span class="badge badge-{{ $r->status===1?'complete':'partial' }}">{{ $r->status===1?'Complete':'Partial' }}</span></td>
          <td>{{ $r->started_at?->format('M d, Y H:i') ?? '—' }}</td>
          <td>{{ $r->completed_at?->format('H:i') ?? '—' }}</td>
          <td style="font-size:12px">{{ $cityVal }}</td>
          <td style="font-size:12px">{{ $brgyVal }}</td>
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
