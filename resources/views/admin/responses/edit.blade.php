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
          <div style="display:flex;flex-direction:column;gap:6px" id="radio-group-{{ $q->id }}">
            @foreach($q->options as $opt)
              <label style="display:flex;align-items:center;gap:8px;font-weight:400;text-transform:none;letter-spacing:0;font-size:13px;cursor:pointer">
                <input type="radio" name="q_{{ $q->id }}" value="{{ $opt->id }}"
                  data-varcode="{{ $q->variable_code }}" data-optcode="{{ $opt->option_code }}"
                  {{ $selectedOptionId == $opt->id ? 'checked' : '' }}
                  style="width:auto;margin:0">
                {{ $opt->label }}
                @if($opt->option_code)<span style="color:#aaa;font-size:11px;font-family:monospace">({{ $opt->option_code }})</span>@endif
              </label>
            @endforeach
            <label style="display:flex;align-items:center;gap:8px;font-weight:400;text-transform:none;letter-spacing:0;font-size:13px;color:#888;cursor:pointer">
              <input type="radio" name="q_{{ $q->id }}" value=""
                data-varcode="{{ $q->variable_code }}" data-optcode=""
                {{ !$selectedOptionId ? 'checked' : '' }}
                style="width:auto;margin:0">
              <em>No answer</em>
            </label>
          </div>
          @if($q->variable_code === 'Q11_MEMBER_CATEGORY')
            <div id="category-age-warning" style="margin-top:8px;font-size:12px;color:#856404;background:#fff3cd;padding:6px 10px;border-radius:4px;display:none"></div>
          @endif

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
          <textarea name="q_{{ $q->id }}" data-varcode="{{ $q->variable_code }}" rows="3" style="font-size:13px">{{ old('q_'.$q->id, $ans?->value_text) }}</textarea>

        @elseif($q->type === 'number')
          <input type="number" name="q_{{ $q->id }}" data-varcode="{{ $q->variable_code }}" value="{{ old('q_'.$q->id, $ans?->value_text) }}" style="max-width:200px">

        @elseif($q->type === 'date')
          <input type="date" name="q_{{ $q->id }}" data-varcode="{{ $q->variable_code }}" value="{{ old('q_'.$q->id, $ans?->value_text) }}" style="max-width:200px">
          @if($q->variable_code === 'Q12_BIRTHDATE')
            <div id="age-calc-result" style="margin-top:8px;font-size:13px;color:#555;display:none"></div>
            <div id="age-calc-error" style="margin-top:6px;font-size:12px;color:#dc3545;display:none"></div>
          @endif
          @if($q->variable_code === 'Q13_DATE_MEASUREMENT')
            <div id="date-order-error" style="margin-top:6px;font-size:12px;color:#dc3545;display:none">
              Date of measurement must be after birthdate.
            </div>
          @endif

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
@push('styles')
<style>
  .validation-banner { padding: 10px 14px; border-radius: 5px; font-size: 13px; margin-top: 8px; }
  .validation-ok   { background: #d4edda; color: #155724; }
  .validation-warn { background: #fff3cd; color: #856404; }
  .validation-err  { background: #f8d7da; color: #721c24; }
</style>
@endpush
<script>
(function () {
  function calcAgeMonths(birthDate, measureDate) {
    var y1 = birthDate.getFullYear(),  m1 = birthDate.getMonth(),  d1 = birthDate.getDate();
    var y2 = measureDate.getFullYear(), m2 = measureDate.getMonth(), d2 = measureDate.getDate();
    var months = (y2 - y1) * 12 + (m2 - m1);
    if (d2 < d1) months--;
    return months;
  }

  function getField(varcode) {
    return document.querySelector('[data-varcode="' + varcode + '"]');
  }

  function getSelectedOptCode(varcode) {
    var checked = document.querySelector('input[type=radio][data-varcode="' + varcode + '"]:checked');
    return checked ? checked.dataset.optcode : null;
  }

  function validate() {
    var birthdateEl  = getField('Q12_BIRTHDATE');
    var measureEl    = getField('Q13_DATE_MEASUREMENT');
    var ageYearsEl   = getField('Q14A_AGE_YEARS');
    var ageMonthsEl  = getField('Q14B_AGE_MONTHS');
    var ageResult    = document.getElementById('age-calc-result');
    var ageError     = document.getElementById('age-calc-error');
    var dateOrderErr = document.getElementById('date-order-error');
    var categoryWarn = document.getElementById('category-age-warning');

    if (!birthdateEl || !measureEl) return;

    // Reset
    if (ageResult)    { ageResult.style.display = 'none'; ageResult.className = ''; ageResult.textContent = ''; }
    if (ageError)     { ageError.style.display = 'none';  ageError.textContent = ''; }
    if (dateOrderErr) { dateOrderErr.style.display = 'none'; }
    if (categoryWarn) { categoryWarn.style.display = 'none'; categoryWarn.textContent = ''; }

    var birthVal   = birthdateEl.value;
    var measureVal = measureEl.value;
    if (!birthVal || !measureVal) return;

    var birth   = new Date(birthVal);
    var measure = new Date(measureVal);

    // 1. Date order check
    if (birth >= measure) {
      if (dateOrderErr) dateOrderErr.style.display = 'block';
      if (ageError)     { ageError.style.display = 'block'; ageError.textContent = 'Birthdate must be before date of measurement.'; }
      return;
    }

    // 2. Calculate age
    var totalMonths = calcAgeMonths(birth, measure);
    var ageYears    = Math.floor(totalMonths / 12);

    // Auto-fill age fields
    if (ageYearsEl)  ageYearsEl.value  = ageYears;
    if (ageMonthsEl) ageMonthsEl.value = totalMonths;

    // 3. Show calculated age
    if (ageResult) {
      ageResult.style.display = 'block';
      ageResult.className     = 'validation-banner validation-ok';
      ageResult.textContent   = 'Calculated age: ' + ageYears + ' yr' + (ageYears !== 1 ? 's' : '')
        + ', ' + totalMonths + ' month' + (totalMonths !== 1 ? 's' : '') + ' (completed)';
    }

    // 4. Category vs age cross-check
    var cat = getSelectedOptCode('Q11_MEMBER_CATEGORY');
    if (!cat || !categoryWarn) return;

    var msg = null;
    if (cat === 'CU5_0_23' && (totalMonths < 0 || totalMonths > 23)) {
      msg = 'Category is CU5 (0–23 mo) but calculated age is ' + totalMonths + ' months.';
    } else if (cat === 'CU5_24_59' && (totalMonths < 24 || totalMonths > 59)) {
      msg = 'Category is CU5 (24–59 mo) but calculated age is ' + totalMonths + ' months.';
    } else if (cat === 'WRA' && (ageYears < 15 || ageYears > 49)) {
      msg = 'Category is WRA (15–49 yrs) but calculated age is ' + ageYears + ' years.';
    } else if (cat !== 'CU5_0_23' && totalMonths >= 0 && totalMonths <= 23) {
      msg = 'Calculated age (' + totalMonths + ' months) suggests CU5 (0–23 mo), but a different category is selected.';
    } else if (cat !== 'CU5_24_59' && totalMonths >= 24 && totalMonths <= 59) {
      msg = 'Calculated age (' + totalMonths + ' months) suggests CU5 (24–59 mo), but a different category is selected.';
    } else if (cat !== 'WRA' && ageYears >= 15 && ageYears <= 49 && totalMonths > 59) {
      msg = 'Calculated age (' + ageYears + ' years) suggests WRA (15–49 yrs), but a different category is selected.';
    }

    if (msg) {
      categoryWarn.style.display = 'block';
      categoryWarn.textContent   = '⚠ ' + msg;
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    var birthdateEl = document.querySelector('[data-varcode="Q12_BIRTHDATE"]');
    var measureEl   = document.querySelector('[data-varcode="Q13_DATE_MEASUREMENT"]');
    document.querySelectorAll('input[type=radio][data-varcode="Q11_MEMBER_CATEGORY"]')
      .forEach(function (el) { el.addEventListener('change', validate); });
    if (birthdateEl) birthdateEl.addEventListener('change', validate);
    if (measureEl)   measureEl.addEventListener('change', validate);
    validate(); // run on load to show existing data state
  });
})();
</script>
@endsection
