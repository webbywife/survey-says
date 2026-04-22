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
          @php $nStep = $q->config['step'] ?? '1'; @endphp
          <input type="number" name="q_{{ $q->id }}" data-varcode="{{ $q->variable_code }}"
            step="{{ $nStep }}" inputmode="decimal"
            value="{{ old('q_'.$q->id, $ans?->value_text) }}" style="max-width:200px">
          @if(in_array($q->variable_code, ['Q16B_WT2','Q17B_HT2']))
            <div id="nerr-{{ $q->id }}" style="display:none;font-size:12px;color:#dc3545;margin-top:4px"></div>
          @endif
          @if($q->variable_code === 'Q16C_WT3')
            <div id="wt-range-warn" style="display:none;padding:8px 12px;border-radius:4px;font-size:12px;background:#fff3cd;color:#856404;margin-top:6px;line-height:1.5"></div>
          @endif
          @if($q->variable_code === 'Q17C_HT3')
            <div id="ht-range-warn" style="display:none;padding:8px 12px;border-radius:4px;font-size:12px;background:#fff3cd;color:#856404;margin-top:6px;line-height:1.5"></div>
          @endif

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

  // ── Weight / Height validation ────────────────────────────────────────────
  function getNumEl(varcode) {
    return document.querySelector('input[type=number][data-varcode="' + varcode + '"]');
  }
  function setFieldErr(el, msg) {
    var errEl = document.getElementById('nerr-' + el.name.replace('q_', ''));
    el.style.borderColor = '#dc3545';
    if (errEl) { errEl.textContent = msg; errEl.style.display = ''; }
  }
  function clearFieldErr(el) {
    var errEl = document.getElementById('nerr-' + el.name.replace('q_', ''));
    el.style.borderColor = '';
    if (errEl) { errEl.textContent = ''; errEl.style.display = 'none'; }
  }

  function validateWeights() {
    var wt1El = getNumEl('Q16A_WT1'), wt2El = getNumEl('Q16B_WT2');
    if (!wt1El || !wt2El || !wt1El.value || !wt2El.value) return;
    var diff = Math.round(Math.abs(parseFloat(wt2El.value) - parseFloat(wt1El.value)) * 100) / 100;
    if (diff > 0.1) {
      setFieldErr(wt2El, 'Wt2 differs from Wt1 (' + parseFloat(wt1El.value).toFixed(2) + ' kg) by ' + diff.toFixed(2) + ' kg — must be ≤ 0.1 kg. Please verify.');
    } else {
      clearFieldErr(wt2El);
    }
  }

  function validateHeights() {
    var ht1El = getNumEl('Q17A_HT1'), ht2El = getNumEl('Q17B_HT2');
    if (!ht1El || !ht2El || !ht1El.value || !ht2El.value) return;
    var diff = Math.round(Math.abs(parseFloat(ht2El.value) - parseFloat(ht1El.value)) * 10) / 10;
    if (diff > 0.5) {
      setFieldErr(ht2El, 'Ht2 differs from Ht1 (' + parseFloat(ht1El.value).toFixed(1) + ' cm) by ' + diff.toFixed(1) + ' cm — must be ≤ 0.5 cm. Please verify.');
    } else {
      clearFieldErr(ht2El);
    }
  }

  var WT_REFS = [
    [0,  [2.5, 4.4], [2.4, 4.2]],
    [6,  [6.4, 9.8], [5.8, 9.2]],
    [12, [7.7,12.0], [7.0,11.5]],
    [18, [8.8,13.7], [8.1,13.2]],
    [23, [9.5,14.8], [8.9,14.4]],
  ];
  var HT_REFS = [
    [0,  [46.1,53.7], [45.4,52.9]],
    [6,  [63.3,71.9], [61.2,70.3]],
    [12, [71.0,80.5], [68.9,79.2]],
    [18, [76.9,87.7], [74.9,86.5]],
    [23, [81.0,92.9], [79.3,91.8]],
  ];

  function interpRange(refs, months, sex) {
    var idx = sex === 'MALE' ? 1 : 2;
    for (var i = 1; i < refs.length; i++) {
      if (months <= refs[i][0]) {
        var lo = refs[i-1], hi = refs[i], t = (months - lo[0]) / (hi[0] - lo[0]);
        return [lo[idx][0] + t*(hi[idx][0]-lo[idx][0]), lo[idx][1] + t*(hi[idx][1]-lo[idx][1])];
      }
    }
    return refs[refs.length-1][idx];
  }

  function validateWeightRange() {
    var ageEl = getNumEl('Q14B_AGE_MONTHS');
    var sexEl = document.querySelector('input[type=radio][data-varcode="Q15_SEX"]:checked');
    var warnEl = document.getElementById('wt-range-warn');
    if (!warnEl || !ageEl || ageEl.value === '' || !sexEl) { if (warnEl) warnEl.style.display = 'none'; return; }
    var age = parseInt(ageEl.value, 10), sex = sexEl.dataset.optcode;
    if (isNaN(age) || age < 0 || age > 23) { warnEl.style.display = 'none'; return; }
    var range = interpRange(WT_REFS, age, sex), out = [];
    ['Q16A_WT1','Q16B_WT2','Q16C_WT3'].forEach(function(vc) {
      var el = getNumEl(vc); if (!el || el.value === '') return;
      var v = parseFloat(el.value);
      if (!isNaN(v) && (v < range[0] || v > range[1])) out.push(vc.split('_')[1] + ' = ' + v.toFixed(2) + ' kg');
    });
    if (out.length) {
      warnEl.style.display = '';
      warnEl.innerHTML = '⚠ Weight outside reference range for a ' + age + '-month '
        + (sex === 'MALE' ? 'boy' : 'girl') + ' (expected ' + range[0].toFixed(1) + '–' + range[1].toFixed(1)
        + ' kg, 3rd–97th %ile): ' + out.join(', ') + '. Please verify before saving.';
    } else { warnEl.style.display = 'none'; }
  }

  function validateHeightRange() {
    var ageEl = getNumEl('Q14B_AGE_MONTHS');
    var sexEl = document.querySelector('input[type=radio][data-varcode="Q15_SEX"]:checked');
    var warnEl = document.getElementById('ht-range-warn');
    if (!warnEl || !ageEl || ageEl.value === '' || !sexEl) { if (warnEl) warnEl.style.display = 'none'; return; }
    var age = parseInt(ageEl.value, 10), sex = sexEl.dataset.optcode;
    if (isNaN(age) || age < 0 || age > 23) { warnEl.style.display = 'none'; return; }
    var range = interpRange(HT_REFS, age, sex), out = [];
    ['Q17A_HT1','Q17B_HT2','Q17C_HT3'].forEach(function(vc) {
      var el = getNumEl(vc); if (!el || el.value === '') return;
      var v = parseFloat(el.value);
      if (!isNaN(v) && (v < range[0] || v > range[1])) out.push(vc.split('_')[1] + ' = ' + v.toFixed(1) + ' cm');
    });
    if (out.length) {
      warnEl.style.display = '';
      warnEl.innerHTML = '⚠ Height outside reference range for a ' + age + '-month '
        + (sex === 'MALE' ? 'boy' : 'girl') + ' (expected ' + range[0].toFixed(1) + '–' + range[1].toFixed(1)
        + ' cm, 3rd–97th %ile): ' + out.join(', ') + '. Please verify before saving.';
    } else { warnEl.style.display = 'none'; }
  }

  document.addEventListener('DOMContentLoaded', function () {
    var wt1 = getNumEl('Q16A_WT1'), wt2 = getNumEl('Q16B_WT2'), wt3 = getNumEl('Q16C_WT3');
    var ht1 = getNumEl('Q17A_HT1'), ht2 = getNumEl('Q17B_HT2'), ht3 = getNumEl('Q17C_HT3');
    var ageEl = getNumEl('Q14B_AGE_MONTHS');
    if (wt1) wt1.addEventListener('change', validateWeights);
    if (wt2) wt2.addEventListener('change', validateWeights);
    if (ht1) ht1.addEventListener('change', validateHeights);
    if (ht2) ht2.addEventListener('change', validateHeights);
    [wt1,wt2,wt3].forEach(function(el) { if (el) el.addEventListener('change', validateWeightRange); });
    [ht1,ht2,ht3].forEach(function(el) { if (el) el.addEventListener('change', validateHeightRange); });
    if (ageEl) { ageEl.addEventListener('change', validateWeightRange); ageEl.addEventListener('change', validateHeightRange); }
    document.querySelectorAll('input[type=radio][data-varcode="Q15_SEX"]').forEach(function(el) {
      el.addEventListener('change', validateWeightRange); el.addEventListener('change', validateHeightRange);
    });
    validateWeights(); validateHeights(); validateWeightRange(); validateHeightRange();
  });
})();
</script>
@endsection
