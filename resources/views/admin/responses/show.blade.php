@extends('layouts.admin')
@section('title', 'Response — ' . $response->serial)
@section('topbar-actions')
  <a href="{{ route('admin.surveys.responses.index', $survey) }}" class="btn btn-secondary btn-sm">← All Responses</a>
@endsection
@section('content')
<div class="stats-row">
  <div class="stat-card"><div class="stat-label">Serial</div><div class="stat-value" style="font-size:16px;font-family:monospace">{{ $response->serial }}</div></div>
  <div class="stat-card"><div class="stat-label">Status</div><div class="stat-value" style="font-size:16px;padding-top:6px"><span class="badge badge-{{ $response->status===1?'complete':'partial' }}">{{ $response->status===1?'Complete':'Partial' }}</span></div></div>
  <div class="stat-card"><div class="stat-label">Duration</div><div class="stat-value" style="font-size:18px">{{ $response->duration_seconds ? gmdate('i:s', $response->duration_seconds) : '—' }}</div></div>
  <div class="stat-card"><div class="stat-label">Date</div><div class="stat-value" style="font-size:13px;padding-top:8px">{{ $response->started_at?->format('M d, Y H:i') ?? '—' }}</div></div>
</div>
<div class="card">
  @php $answersMap = $response->answers->keyBy('question_id'); @endphp
  @foreach($questions as $q)
    @php $ans = $answersMap->get($q->id); @endphp
    <div style="padding:14px 0;border-bottom:1px solid #f5f5f5">
      <div style="font-size:11px;color:#aaa;font-family:monospace;margin-bottom:2px">{{ $q->variable_code }}</div>
      <div style="font-size:14px;font-weight:600;color:#333;margin-bottom:6px">{{ $q->label }}</div>
      @if(!$ans)
        <span style="color:#ccc;font-size:13px;font-style:italic">No answer</span>
      @elseif($q->type==='single_choice')
        @php $sel=$ans->selectedOptions->first(); $opt=$sel?$q->options->firstWhere('id',$sel->question_option_id):null; @endphp
        <span style="background:#f0f0f0;padding:4px 10px;border-radius:4px;font-size:13px">{{ $opt?$opt->label.' ('.$opt->option_code.')':'—' }}</span>
      @elseif($q->type==='multi_select')
        @foreach($ans->selectedOptions as $s) @php $opt=$q->options->firstWhere('id',$s->question_option_id); @endphp
          @if($opt)<span style="background:#e8f4fd;padding:3px 8px;border-radius:12px;font-size:12px;margin:2px;display:inline-block">{{ $opt->label }}</span>@endif
        @endforeach
      @elseif($q->isGrid())
        <table style="font-size:12px;border-collapse:collapse">
          <thead><tr><th style="padding:5px 10px;background:#f8f8f8;border:1px solid #eee">Row</th>
            @foreach($q->options as $col)<th style="padding:5px 10px;background:#f8f8f8;border:1px solid #eee">{{ $col->label }}</th>@endforeach
          </tr></thead>
          <tbody>
          @foreach($q->gridRows as $row)
            <tr><td style="padding:5px 10px;border:1px solid #eee;font-weight:600">{{ $row->label }}</td>
              @foreach($q->options as $col)
                @php $cell=$ans->gridCells->first(fn($c)=>$c->grid_row_id===$row->id&&$c->question_option_id===$col->id); @endphp
                <td style="padding:5px 10px;border:1px solid #eee;text-align:center">{{ $cell?->cell_value??'—' }}</td>
              @endforeach
            </tr>
          @endforeach
          </tbody>
        </table>
      @else
        <span style="font-size:14px;color:#333">{{ $ans->value_text??'—' }}</span>
      @endif
    </div>
  @endforeach
</div>
@endsection
