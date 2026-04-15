@extends('layouts.admin')
@section('title', 'Edit Response — ' . $response->serial)
@section('topbar-actions')
  <a href="{{ route('admin.surveys.responses.show', [$survey, $response]) }}" class="btn btn-secondary btn-sm">← View Response</a>
@endsection
@section('content')
<div class="stats-row">
  <div class="stat-card"><div class="stat-label">Serial</div><div class="stat-value" style="font-size:16px;font-family:monospace">{{ $response->serial }}</div></div>
  <div class="stat-card"><div class="stat-label">Status</div><div class="stat-value" style="font-size:16px;padding-top:6px"><span class="badge badge-{{ $response->status===1?'complete':'partial' }}">{{ $response->status===1?'Complete':'Partial' }}</span></div></div>
  <div class="stat-card"><div class="stat-label">Duration</div><div class="stat-value" style="font-size:18px">{{ $response->duration_seconds ? gmdate('i:s', $response->duration_seconds) : '—' }}</div></div>
  <div class="stat-card"><div class="stat-label">Date</div><div class="stat-value" style="font-size:13px;padding-top:8px">{{ $response->started_at?->format('M d, Y H:i') ?? '—' }}</div></div>
</div>

<form method="POST" action="{{ route('admin.surveys.responses.update', [$survey, $response]) }}">
  @csrf @method('PUT')
  <div class="card">
    @foreach($questions as $q)
      @php $ans = $answersMap->get($q->id); @endphp
      <div style="padding:16px 0;border-bottom:1px solid #f5f5f5">
        <div style="font-size:11px;color:#aaa;font-family:monospace;margin-bottom:2px">{{ $q->variable_code }}</div>
        <div style="font-size:14px;font-weight:600;color:#333;margin-bottom:8px">
          {{ $q->label }}
          @if($q->is_required)<span style="color:#dc3545;margin-left:4px">*</span>@endif
        </div>

        @if($q->type === 'single_choice')
          @php $selectedOptionId = $ans?->selectedOptions->first()?->question_option_id; @endphp
          <div style="display:flex;flex-direction:column;gap:6px">
            @foreach($q->options as $opt)
              <label style="display:flex;align-items:center;gap:8px;font-weight:400;text-transform:none;letter-spacing:0;font-size:13px;cursor:pointer">
                <input type="radio" name="q_{{ $q->id }}" value="{{ $opt->id }}"
                  {{ $selectedOptionId == $opt->id ? 'checked' : '' }}
                  style="width:auto;margin:0">
                {{ $opt->label }}
                @if($opt->option_code)<span style="color:#aaa;font-size:11px;font-family:monospace">({{ $opt->option_code }})</span>@endif
              </label>
            @endforeach
            <label style="display:flex;align-items:center;gap:8px;font-weight:400;text-transform:none;letter-spacing:0;font-size:13px;color:#888;cursor:pointer">
              <input type="radio" name="q_{{ $q->id }}" value=""
                {{ !$selectedOptionId ? 'checked' : '' }}
                style="width:auto;margin:0">
              <em>No answer</em>
            </label>
          </div>

        @elseif($q->type === 'multi_select')
          @php $selectedIds = $ans?->selectedOptions->pluck('question_option_id')->toArray() ?? []; @endphp
          <div style="display:flex;flex-direction:column;gap:6px">
            @foreach($q->options as $opt)
              <label style="display:flex;align-items:center;gap:8px;font-weight:400;text-transform:none;letter-spacing:0;font-size:13px;cursor:pointer">
                <input type="checkbox" name="q_{{ $q->id }}[]" value="{{ $opt->id }}"
                  {{ in_array($opt->id, $selectedIds) ? 'checked' : '' }}
                  style="width:auto;margin:0">
                {{ $opt->label }}
                @if($opt->option_code)<span style="color:#aaa;font-size:11px;font-family:monospace">({{ $opt->option_code }})</span>@endif
              </label>
            @endforeach
          </div>

        @elseif($q->isGrid())
          <div style="overflow-x:auto">
            <table style="font-size:12px;border-collapse:collapse;min-width:400px">
              <thead>
                <tr>
                  <th style="padding:6px 10px;background:#f8f8f8;border:1px solid #eee;text-align:left">Row</th>
                  @foreach($q->options as $col)
                    <th style="padding:6px 10px;background:#f8f8f8;border:1px solid #eee;text-align:center">{{ $col->label }}</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @foreach($q->gridRows as $row)
                  <tr>
                    <td style="padding:6px 10px;border:1px solid #eee;font-weight:600">{{ $row->label }}</td>
                    @foreach($q->options as $col)
                      @php
                        $cell = $ans?->gridCells->first(fn($c) => $c->grid_row_id === $row->id && $c->question_option_id === $col->id);
                      @endphp
                      <td style="padding:4px 6px;border:1px solid #eee;text-align:center">
                        <input type="text" name="g_{{ $q->id }}_{{ $row->id }}_{{ $col->id }}"
                          value="{{ $cell?->cell_value }}"
                          style="width:80px;padding:4px 6px;font-size:12px;text-align:center">
                      </td>
                    @endforeach
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        @elseif($q->type === 'textarea')
          <textarea name="q_{{ $q->id }}" rows="3" style="font-size:13px">{{ old('q_'.$q->id, $ans?->value_text) }}</textarea>

        @elseif($q->type === 'number')
          <input type="number" name="q_{{ $q->id }}" value="{{ old('q_'.$q->id, $ans?->value_text) }}" style="max-width:200px">

        @elseif($q->type === 'date')
          <input type="date" name="q_{{ $q->id }}" value="{{ old('q_'.$q->id, $ans?->value_text) }}" style="max-width:200px">

        @elseif($q->type === 'datetime')
          <input type="datetime-local" name="q_{{ $q->id }}" value="{{ old('q_'.$q->id, $ans?->value_text) }}" style="max-width:260px">

        @elseif($q->type === 'ph_location')
          <input type="text" name="q_{{ $q->id }}" value="{{ old('q_'.$q->id, $ans?->value_text) }}"
            placeholder="Province / City / Barangay">

        @else
          <input type="text" name="q_{{ $q->id }}" value="{{ old('q_'.$q->id, $ans?->value_text) }}">
        @endif
      </div>
    @endforeach

    <div style="display:flex;gap:10px;margin-top:20px;padding-top:20px;border-top:1px solid #f0f0f0">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="{{ route('admin.surveys.responses.show', [$survey, $response]) }}" class="btn btn-secondary">Cancel</a>
    </div>
  </div>
</form>
@endsection
