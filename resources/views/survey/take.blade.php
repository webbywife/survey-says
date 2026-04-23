<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>{{ $survey->title }}</title>
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#550D0E">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Lato',sans-serif;background:#f5f5f7;min-height:100vh;color:#222}
    .s-header{background:#550D0E;color:#fff;padding:16px 0}
    .s-header .inner{max-width:740px;margin:0 auto;padding:0 20px;display:flex;align-items:center;gap:12px}
    .s-seal{width:32px;height:32px;background:#C9A84C;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-weight:700;color:#550D0E;flex-shrink:0}
    .s-title{font-family:'Playfair Display',serif;font-size:17px}
    .prog-wrap{background:#e0e0e0;height:6px}
    .prog-bar{background:#C9A84C;height:6px;transition:width .4s}
    .prog-info{max-width:740px;margin:6px auto 0;padding:0 20px;display:flex;justify-content:space-between;font-size:11px;color:#999;letter-spacing:.03em}
    .s-body{max-width:740px;margin:28px auto;padding:0 20px}
    .q-card{background:#fff;border-radius:8px;padding:24px 28px;box-shadow:0 1px 4px rgba(0,0,0,.08);margin-bottom:14px}
    .q-num{font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:#C9A84C;margin-bottom:5px}
    .q-text{font-size:16px;color:#222;margin-bottom:4px;line-height:1.55}
    .q-help{font-size:13px;color:#888;margin-bottom:14px}
    .choice-list{list-style:none;margin-top:12px}
    .choice-list li{margin-bottom:7px}
    .choice-list label{display:flex;align-items:center;gap:10px;cursor:pointer;padding:9px 13px;border:1px solid #e8e8e8;border-radius:6px;transition:background .12s,border-color .12s;font-size:14px}
    .choice-list label:hover{background:#faf6ef;border-color:#C9A84C}
    input[type=radio],input[type=checkbox]{accent-color:#7B1213;width:16px;height:16px;flex-shrink:0}
    input[type=text],input[type=number],input[type=date],input[type=time],textarea,select{width:100%;padding:10px 13px;border:1px solid #ddd;border-radius:6px;font-size:14px;font-family:inherit;outline:none;transition:border-color .15s;background:#fff}
    input:focus,textarea:focus,select:focus{border-color:#7B1213}
    textarea{resize:vertical;min-height:90px}
    .rating-row{display:flex;gap:7px;flex-wrap:wrap;margin-top:12px}
    .r-btn{width:44px;height:44px;border:2px solid #ddd;border-radius:7px;background:#fff;font-size:15px;font-weight:700;cursor:pointer;transition:all .12s}
    .r-btn:hover,.r-btn.sel{background:#7B1213;border-color:#7B1213;color:#fff}
    .rating-lbl{display:flex;justify-content:space-between;font-size:11px;color:#999;margin-top:4px}
    .grid-tbl{width:100%;border-collapse:collapse;font-size:13px;margin-top:12px}
    .grid-tbl th{padding:8px 10px;background:#f8f8f8;text-align:center;border:1px solid #eee}
    .grid-tbl td{padding:7px 10px;border:1px solid #eee}
    .grid-tbl td:first-child{font-weight:600;background:#fafafa}
    .grid-tbl input{width:70px;padding:4px 7px;text-align:center;font-size:12px}
    .submit-wrap{text-align:center;margin:32px 0 48px}
    .btn-sub{background:#7B1213;color:#fff;border:none;padding:14px 48px;border-radius:6px;font-size:16px;font-weight:700;cursor:pointer;font-family:'Lato',sans-serif;transition:background .15s}
    .btn-sub:hover{background:#550D0E}
    .btn-sub:disabled{background:#aaa;cursor:not-allowed}
    .skip-hide{display:none!important}
    /* Offline banner */
    #offline-bar{display:none;position:sticky;top:0;z-index:999;background:#b45309;color:#fff;text-align:center;padding:8px 16px;font-size:13px;font-weight:700;letter-spacing:.03em}
    #offline-bar.show{display:block}
    /* Sync queue banner */
    #sync-bar{display:none;position:sticky;top:0;z-index:998;background:#1e3a5f;color:#fff;padding:10px 20px;font-size:13px;align-items:center;gap:12px;justify-content:center;flex-wrap:wrap}
    #sync-bar.show{display:flex}
    #sync-bar button{background:#fff;color:#1e3a5f;border:none;padding:5px 14px;border-radius:4px;font-size:12px;font-weight:700;cursor:pointer}
    #sync-bar button:disabled{opacity:.5;cursor:not-allowed}
    /* Collection mode bar */
    #mode-bar{background:#3d0608;border-bottom:1px solid rgba(255,255,255,.08);padding:0 20px}
    #mode-bar .inner{max-width:740px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:12px;height:44px}
    #mode-bar .mode-label{font-size:12px;color:rgba(255,255,255,.6);letter-spacing:.04em}
    .mode-pills{display:flex;border-radius:8px;overflow:hidden;border:1px solid rgba(255,255,255,.2)}
    .mode-pill{padding:7px 20px;font-size:13px;font-weight:700;cursor:pointer;transition:background .2s,color .2s;color:rgba(255,255,255,.5);background:transparent;border:none;font-family:'Lato',sans-serif;letter-spacing:.03em;white-space:nowrap}
    .mode-pill.active-online{background:#2d6a2f;color:#fff}
    .mode-pill.active-offline{background:#b45309;color:#fff}
  </style>
</head>
<body>

<div id="offline-bar">⚡ You are offline — answers will be saved to this device and synced when back online.</div>
<div id="sync-bar">
  <span id="sync-count-txt"></span>
  <button id="sync-btn" onclick="syncNow()">Sync Now</button>
  <span id="sync-status"></span>
</div>

@if(!empty($survey->logo_url))
<div style="background:#fff;border-bottom:1px solid #e8e8e8;padding:14px 20px;text-align:center">
  <img src="{{ $survey->logo_url }}" alt="{{ $survey->title }}" style="max-height:72px;max-width:100%;object-fit:contain">
</div>
@endif
<div class="s-header">
  <div class="inner">
    <div class="s-seal">S</div>
    <div class="s-title">{{ $survey->title }}</div>
  </div>
</div>
<div id="mode-bar">
  <div class="inner">
    <span class="mode-label">Collection Mode</span>
    <div class="mode-pills">
      <button class="mode-pill" id="mode-pill-online"  onclick="setCollectionMode('online')">&#9679; Online</button>
      <button class="mode-pill" id="mode-pill-offline" onclick="setCollectionMode('offline')">&#9675; Offline</button>
    </div>
  </div>
</div>
@if($survey->show_progress_bar)
<div class="prog-wrap"><div class="prog-bar" id="prog" style="width:0%"></div></div>
<div class="prog-info"><span id="prog-answered"></span><span id="prog-pct"></span></div>
@endif

<div class="s-body">
  <div style="background:#fff8e1;border-left:4px solid #C9A84C;border-radius:0 6px 6px 0;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#555;line-height:1.55">
    <strong style="color:#333;display:block;margin-bottom:3px">CONFIDENTIALITY</strong>
    All information obtained in this form is held strictly confidential and cannot be used for taxation, investigation, or law enforcement purposes (RA 10625).
  </div>
  <form method="POST" action="{{ route('survey.submit', $survey->public_token) }}" id="sf">
    @csrf
    @if($errors->any())
      <div style="background:#f8d7da;color:#721c24;padding:12px 16px;border-radius:6px;margin-bottom:16px;font-size:13px">
        {{ $errors->first() }}
      </div>
    @endif
    @php
      $qNum = 0; $lastSectionId = null;
      $hasNamePlaceholder = $questions->contains(fn($q) => str_contains($q->label, '[NAME]'));
      $nameSourceId = null;
      if ($hasNamePlaceholder) {
        $firstPlaceholderPos = $questions->search(fn($q) => str_contains($q->label, '[NAME]'));
        $nameSourceId = $questions->take($firstPlaceholderPos)
          ->filter(fn($q) => stripos($q->label, 'NAME') !== false)
          ->last()?->id;
      }
    @endphp
    @foreach($questions as $q)
      @php $qNum++; $existing = $existingAnswers[$q->id] ?? null; @endphp
      @if($q->section_id && $q->section_id !== $lastSectionId)
        @php $lastSectionId = $q->section_id; $sec = $survey->sections->firstWhere('id', $q->section_id); @endphp
        @if($sec)
          <div style="margin:28px 0 10px">
            <div style="background:#550D0E;color:#fff;padding:10px 18px;border-radius:7px;font-family:'Playfair Display',serif;font-size:15px;font-weight:700;letter-spacing:.02em">
              {{ $sec->title }}
            </div>
            @if($sec->description)
              <div style="font-size:12px;color:#888;margin-top:5px;padding:0 4px;font-style:italic">{{ $sec->description }}</div>
            @endif
          </div>
        @endif
      @endif
      <div class="q-card" id="qc-{{ $q->id }}" data-qid="{{ $q->id }}">
        <div class="q-num">Q{{ $qNum }}@if($q->is_required) <span style="color:#dc3545">*</span>@endif</div>
        <div class="q-text"@if(str_contains($q->label, '[NAME]')) data-tpl="{{ $q->label }}"@endif>{!! nl2br(e($q->label)) !!}</div>
        @if($q->help_text)<div class="q-help">{{ $q->help_text }}</div>@endif

        @if($q->type==='single_choice')
          <ul class="choice-list">
            @foreach($q->options as $opt)
              <li><label>
                <input type="radio" name="q_{{ $q->id }}" value="{{ $opt->option_code }}"
                  data-varcode="{{ $q->variable_code }}" data-optcode="{{ $opt->option_code }}"
                  {{ $existing?->value_text===$opt->option_code?'checked':'' }} onchange="applySkip();validateSurveyDates()">
                {{ $opt->label }}
              </label></li>
            @endforeach
          </ul>

        @elseif($q->type==='multi_select')
          <ul class="choice-list" style="margin-top:12px">
            @foreach($q->options as $opt)
              @php $checked = $existing && $existing->selectedOptions->contains('question_option_id', $opt->id); @endphp
              <li><label>
                <input type="checkbox" name="q_{{ $q->id }}[]" value="{{ $opt->option_code }}" {{ $checked?'checked':'' }}>
                {{ $opt->label }}
              </label></li>
            @endforeach
          </ul>

        @elseif($q->type==='rating')
          @php $cfg=$q->config??[];$min=$cfg['min']??1;$max=$cfg['max']??5;$ev=$existing?->value_text; @endphp
          <div class="rating-row">
            @for($r=$min;$r<=$max;$r++)
              <button type="button" class="r-btn {{ $ev==$r?'sel':'' }}" data-q="{{ $q->id }}" data-v="{{ $r }}" onclick="selRating(this)">{{ $r }}</button>
            @endfor
          </div>
          <div class="rating-lbl"><span>{{ $cfg['min_label']??'Low' }}</span><span>{{ $cfg['max_label']??'High' }}</span></div>
          <input type="hidden" name="q_{{ $q->id }}" id="rv-{{ $q->id }}" value="{{ $ev??'' }}">

        @elseif($q->type==='number')
          @php $cfg=$q->config??[]; @endphp
          <div style="display:flex;align-items:center;gap:8px;margin-top:12px">
          <input type="number" name="q_{{ $q->id }}" data-varcode="{{ $q->variable_code }}"
            style="max-width:200px"
            min="{{ $cfg['min']??'' }}" max="{{ $cfg['max']??'' }}" step="{{ $cfg['step']??'1' }}"
            value="{{ $existing?->value_text??'' }}" {{ $q->is_required?'required':'' }}
            inputmode="decimal" oninput="clampNumber(this)" onchange="clampNumber(this)">
          @if(isset($cfg['min']) || isset($cfg['max']))
            <span class="num-range-hint" style="font-size:11px;color:#aaa">{{ isset($cfg['min']) ? $cfg['min'] : '' }}–{{ isset($cfg['max']) ? $cfg['max'] : '' }}</span>
          @endif
          </div>
          <div class="num-err" id="nerr-{{ $q->id }}" style="display:none;font-size:12px;color:#dc3545;margin-top:4px"></div>
          @if($q->variable_code === 'Q16C_WT3')
            <div id="wt-range-warn" style="display:none;padding:8px 12px;border-radius:4px;font-size:12px;background:#fff3cd;color:#856404;margin-top:6px;line-height:1.5"></div>
          @endif
          @if($q->variable_code === 'Q17C_HT3')
            <div id="ht-range-warn" style="display:none;padding:8px 12px;border-radius:4px;font-size:12px;background:#fff3cd;color:#856404;margin-top:6px;line-height:1.5"></div>
          @endif

        @elseif($q->type==='date')
          @php
            $cfg = is_array($q->config) ? $q->config : [];
            // For the birthdate field in 0–23 month child surveys (no member category question),
            // compute min/max server-side so the picker renders with correct constraints immediately.
            $hasMemberCategory = $questions->contains('variable_code', 'Q11_MEMBER_CATEGORY');
            if ($q->variable_code === 'Q12_BIRTHDATE' && !$hasMemberCategory) {
                $cfg['min'] = now()->subMonths(24)->format('Y-m-d');
                $cfg['max'] = now()->format('Y-m-d');
            }
          @endphp
          <div style="display:flex;align-items:center;gap:8px;margin-top:12px">
          <input type="date" name="q_{{ $q->id }}" style="max-width:220px"
            data-varcode="{{ $q->variable_code }}"
            @if(!empty($cfg['min'])) min="{{ $cfg['min'] }}" @endif
            @if(!empty($cfg['max'])) max="{{ $cfg['max'] }}" @endif
            value="{{ $existing?->value_text??'' }}" {{ $q->is_required?'required':'' }}
            oninput="clampDate(this)" onchange="validateSurveyDates()">
          @if(!empty($cfg['min']) || !empty($cfg['max']))
            <span style="font-size:11px;color:#aaa">{{ $cfg['min']??'' }} to {{ $cfg['max']??'' }}</span>
          @endif
          </div>
          <div class="num-err" id="nerr-{{ $q->id }}" style="display:none;font-size:12px;color:#dc3545;margin-top:4px"></div>

        @elseif($q->type==='time')
          <input type="time" name="q_{{ $q->id }}" style="max-width:180px;margin-top:12px"
            value="{{ $existing?->value_text??'' }}" {{ $q->is_required?'required':'' }}>

        @elseif($q->type==='grid')
          <div style="overflow-x:auto">
            <table class="grid-tbl">
              <thead><tr>
                <th style="text-align:left">{{ $q->config['row_header']??'Item' }}</th>
                @foreach($q->options as $col)<th>{{ $col->label }}</th>@endforeach
              </tr></thead>
              <tbody>
              @foreach($q->gridRows as $row)
                <tr><td>{{ $row->label }}</td>
                  @foreach($q->options as $col)
                    <td style="text-align:center">
                      <input type="text" name="q_{{ $q->id }}[{{ $row->row_code }}][{{ $col->option_code }}]"
                        style="width:65px;text-align:center" maxlength="10" value="">
                    </td>
                  @endforeach
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>

        @elseif($q->type==='ph_location')
          @php
            $locVal = $existing ? json_decode($existing->value_text ?? '{}', true) : [];
          @endphp
          <div style="margin-top:12px;display:flex;flex-direction:column;gap:10px">
            <div>
              <label style="font-size:12px;color:#888;display:block;margin-bottom:4px">Province / NCR</label>
              <select name="q_{{ $q->id }}_province" id="ph-prov-{{ $q->id }}"
                      style="width:100%;padding:10px 13px;border:1px solid #ddd;border-radius:6px;font-size:14px;font-family:inherit"
                      onchange="loadCities({{ $q->id }}, this.value, this.options[this.selectedIndex].text)"
                      {{ $q->is_required?'required':'' }}>
                <option value="">— Select Province —</option>
              </select>
              <input type="hidden" name="q_{{ $q->id }}_province_name" id="ph-prov-name-{{ $q->id }}" value="{{ $locVal['province'] ?? '' }}">
            </div>
            <div>
              <label style="font-size:12px;color:#888;display:block;margin-bottom:4px">City / Municipality</label>
              <select name="q_{{ $q->id }}_city" id="ph-city-{{ $q->id }}"
                      style="width:100%;padding:10px 13px;border:1px solid #ddd;border-radius:6px;font-size:14px;font-family:inherit"
                      onchange="loadBarangays({{ $q->id }}, this.value, this.options[this.selectedIndex].text)"
                      {{ $q->is_required?'required':'' }}>
                <option value="">— Select City / Municipality —</option>
              </select>
              <input type="hidden" name="q_{{ $q->id }}_city_name" id="ph-city-name-{{ $q->id }}" value="{{ $locVal['city'] ?? '' }}">
            </div>
            <div>
              <label style="font-size:12px;color:#888;display:block;margin-bottom:4px">Barangay</label>
              <select name="q_{{ $q->id }}_barangay" id="ph-brgy-{{ $q->id }}"
                      style="width:100%;padding:10px 13px;border:1px solid #ddd;border-radius:6px;font-size:14px;font-family:inherit">
                <option value="">— Select Barangay —</option>
              </select>
              <input type="text" name="q_{{ $q->id }}_barangay_txt" id="ph-brgy-txt-{{ $q->id }}"
                     placeholder="Or type barangay name"
                     style="display:none;width:100%;padding:10px 13px;border:1px solid #ddd;border-radius:6px;font-size:14px;font-family:inherit;margin-top:6px"
                     value="{{ $locVal['barangay'] ?? '' }}">
            </div>
          </div>
          <script>
          (function(){
            const qid = {{ $q->id }};
            const savedProv = '{{ addslashes($locVal['province_code'] ?? '') }}';
            const savedCity = '{{ addslashes($locVal['city_code'] ?? '') }}';
            const savedBrgy = '{{ addslashes($locVal['barangay'] ?? '') }}';

            fetch('/api/psgc/provinces').then(r=>r.json()).then(provinces=>{
              const sel = document.getElementById('ph-prov-'+qid);
              const byRegion = {};
              provinces.forEach(p=>{
                if(!byRegion[p.region_name]) byRegion[p.region_name]=[];
                byRegion[p.region_name].push(p);
              });
              Object.keys(byRegion).sort().forEach(region=>{
                const grp = document.createElement('optgroup');
                grp.label = region;
                byRegion[region].forEach(p=>{
                  const opt = document.createElement('option');
                  opt.value = p.code; opt.textContent = p.name;
                  if(p.code === savedProv) opt.selected = true;
                  grp.appendChild(opt);
                });
                sel.appendChild(grp);
              });
              if(savedProv) loadCities(qid, savedProv, '', savedCity, savedBrgy);
            }).catch(()=>{
              // Offline and not cached — fall back to text input
              const sel = document.getElementById('ph-prov-'+qid);
              sel.style.display='none'; sel.removeAttribute('required'); sel.removeAttribute('name');
              const txt = document.createElement('input');
              txt.type='text'; txt.name='q_'+qid+'_province';
              txt.placeholder='Province (type manually)';
              txt.style='width:100%;padding:10px 13px;border:1px solid #ddd;border-radius:6px;font-size:14px;font-family:inherit';
              txt.value = savedProv || '';
              sel.parentNode.insertBefore(txt, sel.nextSibling);
            });
          })();

          function loadCities(qid, provCode, provName, savedCity, savedBrgy){
            if(!provCode) return;
            const nameEl = document.getElementById('ph-prov-name-'+qid);
            if(nameEl) nameEl.value = provName ||
              (document.getElementById('ph-prov-'+qid)?.options[document.getElementById('ph-prov-'+qid)?.selectedIndex]?.text || '');
            const citySel = document.getElementById('ph-city-'+qid);
            if(!citySel) return;
            citySel.innerHTML = '<option value="">— Select City / Municipality —</option>';
            document.getElementById('ph-brgy-'+qid).innerHTML = '<option value="">— Select Barangay —</option>';
            fetch('/api/psgc/cities/'+encodeURIComponent(provCode)).then(r=>r.json()).then(cities=>{
              cities.forEach(c=>{
                const opt = document.createElement('option');
                opt.value = c.code; opt.textContent = c.name + (c.city_class==='City'?' (City)':'');
                if(c.code === (savedCity||'')) opt.selected = true;
                citySel.appendChild(opt);
              });
              if(savedCity) loadBarangays(qid, savedCity, '', savedBrgy||'');
            }).catch(()=>{
              citySel.style.display='none'; citySel.removeAttribute('name');
              const txt = document.createElement('input');
              txt.type='text'; txt.name='q_'+qid+'_city';
              txt.placeholder='City/Municipality (type manually)';
              txt.style='width:100%;padding:10px 13px;border:1px solid #ddd;border-radius:6px;font-size:14px;font-family:inherit';
              citySel.parentNode.insertBefore(txt, citySel.nextSibling);
            });
          }

          function loadBarangays(qid, cityCode, cityName, savedBrgy){
            if(!cityCode) return;
            const nameEl = document.getElementById('ph-city-name-'+qid);
            if(nameEl) nameEl.value = cityName ||
              (document.getElementById('ph-city-'+qid)?.options[document.getElementById('ph-city-'+qid)?.selectedIndex]?.text || '');
            const brgySel = document.getElementById('ph-brgy-'+qid);
            const brgyTxt = document.getElementById('ph-brgy-txt-'+qid);
            if(!brgySel) return;
            brgySel.innerHTML = '<option value="">Loading…</option>';
            fetch('/api/psgc/barangays/'+encodeURIComponent(cityCode)).then(r=>r.json()).then(barangays=>{
              if(barangays.length === 0){
                brgySel.style.display='none'; brgySel.removeAttribute('name');
                brgyTxt.style.display=''; brgyTxt.name='q_'+qid+'_barangay';
                brgyTxt.value = savedBrgy||'';
              } else {
                brgySel.style.display=''; brgySel.name='q_'+qid+'_barangay';
                brgyTxt.style.display='none'; brgyTxt.removeAttribute('name');
                brgySel.innerHTML='<option value="">— Select Barangay —</option>';
                barangays.forEach(b=>{
                  const opt=document.createElement('option');
                  opt.value=b.name; opt.textContent=b.name;
                  if(b.name===(savedBrgy||'')) opt.selected=true;
                  brgySel.appendChild(opt);
                });
              }
            }).catch(()=>{
              brgySel.style.display='none'; brgySel.removeAttribute('name');
              brgyTxt.style.display=''; brgyTxt.name='q_'+qid+'_barangay';
              brgyTxt.value=savedBrgy||'';
            });
          }
          </script>

        @else
          <textarea name="q_{{ $q->id }}" style="margin-top:12px" {{ $q->is_required?'required':'' }}>{{ $existing?->value_text??'' }}</textarea>
        @endif
      </div>
    @endforeach

    <div class="submit-wrap">
      <button type="submit" class="btn-sub" id="submit-btn">Submit Survey</button>
    </div>
  </form>

  {{-- Shown inline when response is saved offline (no network redirect needed) --}}
  <div id="offline-saved" style="display:none;text-align:center;padding:48px 20px 64px">
    <div style="width:64px;height:64px;border-radius:50%;background:#fff3cd;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
      <svg viewBox="0 0 24 24" fill="none" stroke="#856404" stroke-width="2.5" width="32" height="32"><path d="M1 1l22 22M16.72 11.06A10.94 10.94 0 0 1 19 12.55M5 12.55a10.94 10.94 0 0 1 5.17-2.39M10.71 5.05A16 16 0 0 1 22.56 9M1.42 9a15.91 15.91 0 0 1 4.7-2.88M8.53 16.11a6 6 0 0 1 6.95 0M12 20h.01"/></svg>
    </div>
    <h2 style="font-family:'Playfair Display',serif;font-size:24px;color:#222;margin-bottom:10px">Saved Offline</h2>
    <p style="font-size:15px;color:#666;max-width:360px;margin:0 auto 8px;line-height:1.6">
      Your response has been saved to this device.<br>
      It will upload automatically when you reconnect.
    </p>
    <p id="offline-saved-count" style="font-size:13px;color:#aaa;margin-bottom:24px"></p>
    <a href="/s/{{ $survey->public_token }}" class="btn-sub" style="display:inline-block;text-decoration:none;padding:12px 32px">
      New Response
    </a>
  </div>
</div>

<script>
// ─── Constants ───────────────────────────────────────────────────────────────
const SURVEY_TOKEN = '{{ $survey->public_token }}';
const SURVEY_TITLE = {{ Js::from($survey->title) }};
const SYNC_URL     = '/s/' + SURVEY_TOKEN + '/sync';
const START_TIME   = Date.now();
const MODE_KEY     = 'ss_collection_mode';

// ─── Collection mode (force-offline toggle) ───────────────────────────────────
function isForceOffline() {
  return localStorage.getItem(MODE_KEY) === 'offline';
}

function applyModeUI() {
  const forced = isForceOffline();
  const onlineBtn  = document.getElementById('mode-pill-online');
  const offlineBtn = document.getElementById('mode-pill-offline');
  onlineBtn.className  = 'mode-pill' + (!forced ? ' active-online'  : '');
  offlineBtn.className = 'mode-pill' + ( forced ? ' active-offline' : '');
  onlineBtn.innerHTML  = (!forced ? '&#9679;' : '&#9675;') + ' Online';
  offlineBtn.innerHTML = ( forced ? '&#9679;' : '&#9675;') + ' Offline';
  // Update submit button label and offline bar
  const effectiveOffline = forced || !navigator.onLine;
  const btn = document.getElementById('submit-btn');
  if (btn && !btn.disabled) btn.textContent = effectiveOffline ? 'Save Offline' : 'Submit Survey';
  document.getElementById('offline-bar').classList.toggle('show', effectiveOffline);
  // Disable browser required-field validation when offline so our handler can run
  document.getElementById('sf').noValidate = effectiveOffline;
}

function setCollectionMode(mode) {
  localStorage.setItem(MODE_KEY, mode);
  applyModeUI();
}

// ─── IndexedDB ───────────────────────────────────────────────────────────────
const IDB = {
  _db: null,
  open() {
    if (this._db) return Promise.resolve(this._db);
    return new Promise((resolve, reject) => {
      const req = indexedDB.open('surveysays-offline', 1);
      req.onupgradeneeded = e => {
        const db = e.target.result;
        if (!db.objectStoreNames.contains('pending')) {
          const store = db.createObjectStore('pending', { keyPath: 'id', autoIncrement: true });
          store.createIndex('by_token', 'survey_token', { unique: false });
        }
      };
      req.onsuccess = e => { this._db = e.target.result; resolve(this._db); };
      req.onerror   = () => reject(req.error);
    });
  },
  async add(record) {
    const db = await this.open();
    return new Promise((resolve, reject) => {
      const tx  = db.transaction('pending', 'readwrite');
      const req = tx.objectStore('pending').add(record);
      req.onsuccess = () => resolve(req.result);
      tx.onerror    = () => reject(tx.error);
    });
  },
  async getAll() {
    const db = await this.open();
    return new Promise((resolve, reject) => {
      const tx  = db.transaction('pending', 'readonly');
      const req = tx.objectStore('pending').getAll();
      req.onsuccess = () => resolve(req.result);
      tx.onerror    = () => reject(tx.error);
    });
  },
  async delete(id) {
    const db = await this.open();
    return new Promise((resolve, reject) => {
      const tx = db.transaction('pending', 'readwrite');
      tx.objectStore('pending').delete(id);
      tx.oncomplete = resolve;
      tx.onerror    = () => reject(tx.error);
    });
  },
  async count() {
    const db  = await this.open();
    const all = await this.getAll();
    return all.filter(r => r.survey_token === SURVEY_TOKEN && r.status === 'pending').length;
  }
};

// ─── Serialize form (handles multi-values & bracket notation) ────────────────
function serializeForm(form) {
  const params = new URLSearchParams();
  // Add extra offline metadata
  params.append('_respondent_token', crypto.randomUUID ? crypto.randomUUID() : (Date.now() + '-' + Math.random().toString(36).slice(2)));
  params.append('_saved_at', new Date().toISOString());
  params.append('_duration', Math.round((Date.now() - START_TIME) / 1000));

  const fd = new FormData(form);
  for (const [key, value] of fd.entries()) {
    if (key === '_token') continue; // skip CSRF
    params.append(key, value);
  }
  return params.toString();
}

// ─── Offline/Online UI ───────────────────────────────────────────────────────
function setOfflineUI(offline) {
  const effectiveOffline = offline || isForceOffline();
  document.getElementById('offline-bar').classList.toggle('show', effectiveOffline);
  const btn = document.getElementById('submit-btn');
  if (btn && !btn.disabled) btn.textContent = effectiveOffline ? 'Save Offline' : 'Submit Survey';
  // Disable browser required-field validation when offline so our handler can run
  document.getElementById('sf').noValidate = effectiveOffline;
}

async function refreshSyncBar() {
  const pending = await IDB.getAll();
  const mine = pending.filter(r => r.survey_token === SURVEY_TOKEN && r.status === 'pending');
  const bar  = document.getElementById('sync-bar');
  if (mine.length > 0) {
    document.getElementById('sync-count-txt').textContent =
      mine.length + ' response' + (mine.length > 1 ? 's' : '') + ' saved offline, pending upload';
    bar.classList.add('show');
  } else {
    bar.classList.remove('show');
  }
}

// ─── Sync pending responses ───────────────────────────────────────────────────
async function syncNow() {
  if (!navigator.onLine) return;
  const btn    = document.getElementById('sync-btn');
  const status = document.getElementById('sync-status');
  btn.disabled = true; btn.textContent = 'Syncing…';

  const pending = (await IDB.getAll()).filter(r => r.survey_token === SURVEY_TOKEN && r.status === 'pending');
  let synced = 0, failed = 0;

  for (const record of pending) {
    try {
      const res = await fetch(SYNC_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: record.form_data,
      });
      if (res.ok) {
        await IDB.delete(record.id);
        synced++;
      } else {
        failed++;
      }
    } catch {
      failed++;
    }
  }

  btn.disabled = false; btn.textContent = 'Sync Now';
  if (synced > 0) {
    status.textContent = '✓ ' + synced + ' uploaded';
    status.style.color = '#90e0a0';
    setTimeout(() => { status.textContent = ''; }, 3000);
  }
  if (failed > 0) {
    status.textContent = '✗ ' + failed + ' failed — will retry';
    status.style.color = '#f87171';
  }
  await refreshSyncBar();
}

// ─── Form submit interceptor ──────────────────────────────────────────────────
document.getElementById('sf').addEventListener('submit', async function(e) {
  // Date/age/category validation — hard block on submit
  if (typeof validateSurveyDates === 'function' && !validateSurveyDates()) {
    e.preventDefault();
    const errField = document.querySelector('input[style*="border-color: rgb(220, 53, 69)"], input[style*="border-color:rgb(220,53,69)"], input[style*="border-color: #dc3545"]');
    if (errField) errField.closest('.q-card')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }

  if (navigator.onLine && !isForceOffline()) return; // let regular POST proceed

  e.preventDefault();
  const btn = document.getElementById('submit-btn');
  btn.disabled = true; btn.textContent = 'Saving…';

  try {
    await IDB.add({
      survey_token:    SURVEY_TOKEN,
      survey_title:    SURVEY_TITLE,
      respondent_token: (crypto.randomUUID ? crypto.randomUUID() : Date.now() + '-' + Math.random().toString(36).slice(2)),
      form_data:       serializeForm(this),
      saved_at:        new Date().toISOString(),
      status:          'pending',
    });
    // Show inline success — don't redirect (done page may not be cached offline)
    document.querySelector('.s-body form').style.display = 'none';
    const savedDiv = document.getElementById('offline-saved');
    savedDiv.style.display = '';
    const count = await IDB.count();
    if (count > 1) {
      document.getElementById('offline-saved-count').textContent =
        count + ' response(s) queued on this device — will sync when online.';
    }
  } catch(err) {
    btn.disabled = false; btn.textContent = 'Save Offline';
    alert('Could not save response. Please try again.\n' + err.message);
  }
});

// ─── Skip logic ───────────────────────────────────────────────────────────────
const skipRules = {!! $skipRulesJson !!};
const allCards  = Array.from(document.querySelectorAll('.q-card'));

function getAnswer(qId){
  const radios=document.querySelectorAll(`input[name="q_${qId}"]:checked`);
  if(radios.length) return [...radios].map(r=>r.value);
  const hidden=document.getElementById(`rv-${qId}`);
  if(hidden) return [hidden.value];
  const ta=document.querySelector(`textarea[name="q_${qId}"]`)||document.querySelector(`input[name="q_${qId}"]`);
  return ta?[ta.value]:[];
}

function isAnswered(card){
  const qid=card.dataset.qid;
  const radios=card.querySelectorAll(`input[name="q_${qid}"]:checked`);
  if(radios.length)return true;
  const checks=card.querySelectorAll(`input[type=checkbox][name^="q_${qid}"]:checked`);
  if(checks.length)return true;
  const txt=card.querySelector(`input[type=text],input[type=number],input[type=date],input[type=time],textarea`);
  if(txt&&txt.value.trim())return true;
  return false;
}

function applySkip(){
  const hidden=new Set();
  for(const rule of skipRules){
    const answers=getAnswer(rule.source);
    let match=false;
    for(const v of answers){
      match=rule.type==='equals'?v===rule.value
           :rule.type==='not_equals'?v!==rule.value
           :rule.type==='selected'?v===rule.value
           :rule.type==='contains'?v.includes(rule.value)
           :false;
      if(match)break;
    }
    if(match){
      let inRange=false;
      for(const card of allCards){
        const id=parseInt(card.dataset.qid);
        if(id===rule.source){inRange=true;continue;}
        if(id===rule.target){inRange=false;}
        if(inRange)hidden.add(id);
      }
    }
  }
  let vis=0;
  allCards.forEach(card=>{
    const id=parseInt(card.dataset.qid);
    const hide=hidden.has(id);
    card.classList.toggle('skip-hide',hide);
    if(!hide)vis++;
  });
  const p=document.getElementById('prog');
  if(p){
    const pct=Math.round(vis/allCards.length*100);
    p.style.width=pct+'%';
    const answered=Array.from(allCards).filter(c=>!c.classList.contains('skip-hide')&&isAnswered(c)).length;
    const el1=document.getElementById('prog-answered');
    const el2=document.getElementById('prog-pct');
    if(el1) el1.textContent=answered+' of '+vis+' answered';
    if(el2) el2.textContent=pct+'% complete';
  }
}

function selRating(btn){
  const q=btn.dataset.q;
  document.querySelectorAll(`.r-btn[data-q="${q}"]`).forEach(b=>b.classList.remove('sel'));
  btn.classList.add('sel');
  document.getElementById('rv-'+q).value=btn.dataset.v;
}

document.querySelectorAll('input[type=radio],input[type=checkbox]').forEach(el=>el.addEventListener('change',applySkip));
document.querySelectorAll('input[type=text],input[type=number],input[type=date],input[type=time],textarea').forEach(el=>el.addEventListener('input',applySkip));
applySkip();

// ─── Online/offline event listeners ──────────────────────────────────────────
window.addEventListener('online',  () => { setOfflineUI(false); if (!isForceOffline()) syncNow(); });
window.addEventListener('offline', () => setOfflineUI(true));
applyModeUI();
refreshSyncBar();

// ─── Number clamp (enforces min/max and decimal places regardless of online/offline mode) ───────
function clampNumber(el) {
  const min    = el.min  !== '' ? parseFloat(el.min)  : null;
  const max    = el.max  !== '' ? parseFloat(el.max)  : null;
  const step   = el.step !== '' ? el.step : '1';
  const maxDec = step.includes('.') ? step.split('.')[1].length : 0;
  const errEl  = document.getElementById('nerr-' + el.name.replace('q_',''));

  // Silently truncate excess decimal places while typing
  if (maxDec > 0 && el.value.includes('.')) {
    const parts = el.value.split('.');
    if (parts[1].length > maxDec) {
      el.value = parts[0] + '.' + parts[1].slice(0, maxDec);
    }
  } else if (maxDec === 0 && el.value.includes('.')) {
    el.value = el.value.split('.')[0];
  }

  const val = parseFloat(el.value);
  if (!isNaN(val)) {
    if (max !== null && val > max) {
      el.value = max;
      el.style.borderColor = '#dc3545';
      if (errEl) { errEl.textContent = 'Maximum value is ' + max + '. Corrected automatically.'; errEl.style.display = ''; }
      setTimeout(() => { el.style.borderColor = ''; if (errEl) errEl.style.display = 'none'; }, 2500);
    } else if (min !== null && val < min) {
      el.value = min;
      el.style.borderColor = '#dc3545';
      if (errEl) { errEl.textContent = 'Minimum value is ' + min + '. Corrected automatically.'; errEl.style.display = ''; }
      setTimeout(() => { el.style.borderColor = ''; if (errEl) errEl.style.display = 'none'; }, 2500);
    } else {
      el.style.borderColor = '';
      if (errEl) errEl.style.display = 'none';
    }
  }
}

function clampDate(el) {
  if (!el.value) return;
  const errEl = document.getElementById('nerr-' + el.name.replace('q_',''));
  const val = el.value;
  if (el.min && val < el.min) {
    el.value = el.min;
    el.style.borderColor = '#dc3545';
    if (errEl) { errEl.textContent = 'Date must be on or after ' + el.min + '.'; errEl.style.display = ''; }
    setTimeout(() => { el.style.borderColor = ''; if (errEl) errEl.style.display = 'none'; }, 2500);
  } else if (el.max && val > el.max) {
    el.value = el.max;
    el.style.borderColor = '#dc3545';
    if (errEl) { errEl.textContent = 'Date must be on or before ' + el.max + '.'; errEl.style.display = ''; }
    setTimeout(() => { el.style.borderColor = ''; if (errEl) errEl.style.display = 'none'; }, 2500);
  } else {
    el.style.borderColor = '';
    if (errEl) errEl.style.display = 'none';
  }
}

// ─── [NAME] substitution ─────────────────────────────────────────────────────
@if($nameSourceId)
(function(){
  const nameInput = document.querySelector('[name="q_{{ $nameSourceId }}"]');
  if (!nameInput) return;
  function esc(s){ return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
  function applyName(){
    const name = nameInput.value.trim();
    const display = name
      ? '<strong style="color:#550D0E">' + esc(name) + '</strong>'
      : '<span style="color:#aaa;font-style:italic">[NAME]</span>';
    document.querySelectorAll('.q-text[data-tpl]').forEach(el => {
      const tpl = el.getAttribute('data-tpl');
      el.innerHTML = esc(tpl).replace(/\[NAME\]/g, display).replace(/\n/g, '<br>');
    });
  }
  nameInput.addEventListener('input', applyName);
  applyName();
})();
@endif

// ─── Register Service Worker ──────────────────────────────────────────────────
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js')
    .catch(err => console.warn('SW registration failed:', err));
}

// ─── Date / Age / Category validation ─────────────────────────────────────────
(function () {
  const todayStr = new Date().toISOString().split('T')[0];

  function getDateEl(varcode) {
    return document.querySelector('input[type=date][data-varcode="' + varcode + '"]');
  }
  function getCategoryOptcode() {
    const el = document.querySelector('input[type=radio][data-varcode="Q11_MEMBER_CATEGORY"]:checked');
    return el ? el.dataset.optcode : null;
  }
  function calcAgeMonths(birth, measure) {
    let m = (measure.getFullYear() - birth.getFullYear()) * 12
           + (measure.getMonth() - birth.getMonth());
    if (measure.getDate() < birth.getDate()) m--;
    return m;
  }
  // Safely subtract months from a date (handles month-end edge cases)
  function dateMinusMonths(refDate, months) {
    const d = new Date(refDate);
    const targetMonth = d.getMonth() - months;
    d.setDate(1);
    d.setMonth(targetMonth);
    // clamp to last valid day of that month
    const lastDay = new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();
    d.setDate(Math.min(refDate.getDate(), lastDay));
    return d.toISOString().split('T')[0];
  }
  function dateMinusYears(refDate, years) {
    const d = new Date(refDate);
    d.setFullYear(d.getFullYear() - years);
    return d.toISOString().split('T')[0];
  }
  function setFieldError(el, msg) {
    const errEl = document.getElementById('nerr-' + el.name.replace('q_', ''));
    el.style.borderColor = '#dc3545';
    if (errEl) { errEl.textContent = msg; errEl.style.display = ''; }
  }
  function clearFieldError(el) {
    const errEl = document.getElementById('nerr-' + el.name.replace('q_', ''));
    el.style.borderColor = '';
    if (errEl) { errEl.textContent = ''; errEl.style.display = 'none'; }
  }

  // Update birthdate min/max based on selected category and measurement date
  function updateBirthdateConstraints() {
    const birthdateEl = getDateEl('Q12_BIRTHDATE');
    const measureEl   = getDateEl('Q13_DATE_MEASUREMENT') || getDateEl('Q13_REF_DATE');
    if (!birthdateEl) return;

    const cat     = getCategoryOptcode();
    const refDate = (measureEl && measureEl.value) ? new Date(measureEl.value) : new Date(todayStr);

    if (!cat) {
      // No category selector — survey targets 0–23 month old children only.
      // Dynamically set max = today and min = 24 months ago so the year
      // picker only shows valid years regardless of browser.
      birthdateEl.max = todayStr;
      birthdateEl.min = dateMinusMonths(new Date(todayStr), 24);
      return;
    }
    if (cat === 'CU5_0_23') {
      // age 0–23 months: born between 23 months ago and measurement date
      birthdateEl.min = dateMinusMonths(refDate, 23);
      birthdateEl.max = refDate.toISOString().split('T')[0];
    } else if (cat === 'CU5_24_59') {
      // age 24–59 months: born between 59 months ago and 24 months ago
      birthdateEl.min = dateMinusMonths(refDate, 59);
      birthdateEl.max = dateMinusMonths(refDate, 24);
    } else if (cat === 'WRA') {
      // age 15–49 years
      birthdateEl.min = dateMinusYears(refDate, 49);
      birthdateEl.max = dateMinusYears(refDate, 15);
    }
  }

  window.validateSurveyDates = function () {
    updateBirthdateConstraints();

    const birthdateEl = getDateEl('Q12_BIRTHDATE');
    const measureEl   = getDateEl('Q13_DATE_MEASUREMENT') || getDateEl('Q13_REF_DATE');
    let valid = true;

    if (measureEl && measureEl.value && measureEl.value > todayStr) {
      setFieldError(measureEl, 'Date of measurement cannot be a future date.');
      valid = false;
    } else if (measureEl) {
      clearFieldError(measureEl);
    }

    if (!birthdateEl || !birthdateEl.value) {
      // Clear auto-filled age fields if birthdate is removed
      const ageYearsEl  = document.querySelector('input[type=number][data-varcode="Q14A_AGE_YEARS"]');
      const ageMonthsEl = document.querySelector('input[type=number][data-varcode="Q14B_AGE_MONTHS"]')
                       || document.querySelector('input[type=number][data-varcode="Q14_AGE_MONTHS"]');
      if (ageYearsEl)  { ageYearsEl.value  = ''; ageYearsEl.readOnly  = false; ageYearsEl.style.background  = ''; ageYearsEl.style.color = ''; }
      if (ageMonthsEl) { ageMonthsEl.value = ''; ageMonthsEl.readOnly = false; ageMonthsEl.style.background = ''; ageMonthsEl.style.color = ''; }
      return valid;
    }

    if (birthdateEl.value > todayStr) {
      setFieldError(birthdateEl, 'Birthdate cannot be a future date.');
      return false;
    }

    if (measureEl && measureEl.value && birthdateEl.value >= measureEl.value) {
      setFieldError(birthdateEl, 'Birthdate must be before the date of measurement.');
      return false;
    }

    const effectiveMeasure = (measureEl && measureEl.value) ? measureEl.value : todayStr;
    if (valid) {
      const birth   = new Date(birthdateEl.value);
      const measure = new Date(effectiveMeasure);
      const months  = calcAgeMonths(birth, measure);
      const years   = Math.floor(months / 12);
      const cat     = getCategoryOptcode();

      // Auto-fill Q14a (age in years) and Q14b / Q14_AGE_MONTHS (age in months)
      const ageYearsEl  = document.querySelector('input[type=number][data-varcode="Q14A_AGE_YEARS"]');
      const ageMonthsEl = document.querySelector('input[type=number][data-varcode="Q14B_AGE_MONTHS"]')
                       || document.querySelector('input[type=number][data-varcode="Q14_AGE_MONTHS"]');
      if (ageYearsEl)  { ageYearsEl.value  = years;  ageYearsEl.readOnly  = true; ageYearsEl.style.background  = '#f5f5f5'; ageYearsEl.style.color = '#555'; }
      if (ageMonthsEl) { ageMonthsEl.value = months; ageMonthsEl.readOnly = true; ageMonthsEl.style.background = '#f5f5f5'; ageMonthsEl.style.color = '#555'; }

      if (cat) {
        let ageErr = null;
        if (cat === 'CU5_0_23'  && (months < 0  || months > 23))
          ageErr = 'Age is ' + months + ' months — must be 0–23 months for this category.';
        if (cat === 'CU5_24_59' && (months < 24 || months > 59))
          ageErr = 'Age is ' + months + ' months — must be 24–59 months for this category.';
        if (cat === 'WRA' && (years < 15 || years > 49))
          ageErr = 'Age is ' + years + ' years — must be 15–49 years for this category.';
        if (ageErr) { setFieldError(birthdateEl, ageErr); valid = false; }
        else clearFieldError(birthdateEl);
      } else {
        clearFieldError(birthdateEl);
      }
    }

    if (window.validateWeightRange)  window.validateWeightRange();
    if (window.validateHeightRange) window.validateHeightRange();
    return valid;
  };

  // ── Weight cross-validation (Wt2 must be within 0.1 kg of Wt1) ──────────────
  function getNumEl(varcode) {
    return document.querySelector('input[type=number][data-varcode="' + varcode + '"]');
  }

  window.validateWeights = function () {
    const wt1El = getNumEl('Q16A_WT1');
    const wt2El = getNumEl('Q16B_WT2');
    const wt3El = getNumEl('Q16C_WT3');

    // Wt2 and Wt3 require Wt1 to be filled first
    if (wt2El && wt2El.value && (!wt1El || !wt1El.value)) {
      wt2El.value = '';
      setFieldError(wt2El, 'Please enter Weight 1 (Wt1) before entering Weight 2.');
    }
    if (wt3El && wt3El.value && (!wt1El || !wt1El.value)) {
      wt3El.value = '';
      setFieldError(wt3El, 'Please enter Weight 1 (Wt1) before entering Weight 3.');
    }

    if (!wt1El || !wt1El.value) return;
    const wt1 = parseFloat(wt1El.value);
    if (isNaN(wt1)) return;

    if (wt2El && wt2El.value) {
      const wt2 = parseFloat(wt2El.value);
      if (!isNaN(wt2)) {
        const diff2 = Math.round(Math.abs(wt2 - wt1) * 100) / 100;
        if (diff2 > 0.1) {
          wt2El.value = '';
          setFieldError(wt2El,
            'Value cleared — Wt2 differed from Wt1 (' + wt1.toFixed(2) + ' kg) by '
            + diff2.toFixed(2) + ' kg. Difference must be within 0.1 kg. Please re-measure.');
        } else {
          clearFieldError(wt2El);
        }
      }
    }

    if (wt3El && wt3El.value) {
      const wt3 = parseFloat(wt3El.value);
      if (!isNaN(wt3)) {
        const diff3 = Math.round(Math.abs(wt3 - wt1) * 100) / 100;
        if (diff3 > 0.1) {
          wt3El.value = '';
          setFieldError(wt3El,
            'Value cleared — Wt3 differed from Wt1 (' + wt1.toFixed(2) + ' kg) by '
            + diff3.toFixed(2) + ' kg. Difference must be within 0.1 kg. Please re-measure.');
        } else {
          clearFieldError(wt3El);
        }
      }
    }
  };

  // Init immediately — DOM is already ready (inline script at bottom of body)
  const _measureEl = getDateEl('Q13_DATE_MEASUREMENT');
  if (_measureEl && !_measureEl.max) _measureEl.max = todayStr;
  updateBirthdateConstraints();
  if (_measureEl) _measureEl.addEventListener('change', function () { updateBirthdateConstraints(); validateSurveyDates(); });

  // ── Height cross-validation (Ht2 must be within 0.5 cm of Ht1) ──────────────
  window.validateHeights = function () {
    const ht1El = getNumEl('Q17A_HT1');
    const ht2El = getNumEl('Q17B_HT2');
    const ht3El = getNumEl('Q17C_HT3');

    // Ht2 and Ht3 require Ht1 to be filled first
    if (ht2El && ht2El.value && (!ht1El || !ht1El.value)) {
      ht2El.value = '';
      setFieldError(ht2El, 'Please enter Height 1 (Ht1) before entering Height 2.');
    }
    if (ht3El && ht3El.value && (!ht1El || !ht1El.value)) {
      ht3El.value = '';
      setFieldError(ht3El, 'Please enter Height 1 (Ht1) before entering Height 3.');
    }

    if (!ht1El || !ht1El.value) return;
    const ht1 = parseFloat(ht1El.value);
    if (isNaN(ht1)) return;

    if (ht2El && ht2El.value) {
      const ht2 = parseFloat(ht2El.value);
      if (!isNaN(ht2)) {
        const diff2 = Math.round(Math.abs(ht2 - ht1) * 10) / 10;
        if (diff2 > 0.5) {
          ht2El.value = '';
          setFieldError(ht2El,
            'Value cleared — Ht2 differed from Ht1 (' + ht1.toFixed(1) + ' cm) by '
            + diff2.toFixed(1) + ' cm. Difference must be within 0.5 cm. Please re-measure.');
        } else {
          clearFieldError(ht2El);
        }
      }
    }

    if (ht3El && ht3El.value) {
      const ht3 = parseFloat(ht3El.value);
      if (!isNaN(ht3)) {
        const diff3 = Math.round(Math.abs(ht3 - ht1) * 10) / 10;
        if (diff3 > 0.5) {
          ht3El.value = '';
          setFieldError(ht3El,
            'Value cleared — Ht3 differed from Ht1 (' + ht1.toFixed(1) + ' cm) by '
            + diff3.toFixed(1) + ' cm. Difference must be within 0.5 cm. Please re-measure.');
        } else {
          clearFieldError(ht3El);
        }
      }
    }
  };

  // Weight/height cross-validation via delegated form listener — catches all fields reliably
  var _sfEl = document.getElementById('sf');
  if (_sfEl) {
    _sfEl.addEventListener('change', function(e) {
      var vc = e.target && e.target.dataset && e.target.dataset.varcode;
      if (!vc) return;
      if (['Q16A_WT1','Q16B_WT2','Q16C_WT3'].indexOf(vc) !== -1) window.validateWeights();
      if (['Q17A_HT1','Q17B_HT2','Q17C_HT3'].indexOf(vc) !== -1) window.validateHeights();
    });
  }
  // Kept for validateWeightRange / validateHeightRange listener setup below
  const _wt1El = getNumEl('Q16A_WT1');
  const _wt2El = getNumEl('Q16B_WT2');
  const _ht1El = getNumEl('Q17A_HT1');
  const _ht2El = getNumEl('Q17B_HT2');

  // ── Weight reference range check (3rd–97th percentile) ──────────────
  // Reference data from chart: [ageMonths, [boysMin,boysMax], [girlsMin,girlsMax]]
  const WT_REFS = [
    [0,  [2.5, 4.4], [2.4, 4.2]],
    [6,  [6.4, 9.8], [5.8, 9.2]],
    [12, [7.7,12.0], [7.0,11.5]],
    [18, [8.8,13.7], [8.1,13.2]],
    [23, [9.5,14.8], [8.9,14.4]],
  ];

  window.validateWeightRange = function () {
    const ageEl  = getNumEl('Q14B_AGE_MONTHS');
    const sexEl  = document.querySelector('input[type=radio][data-varcode="Q15_SEX"]:checked');
    const warnEl = document.getElementById('wt-range-warn');
    if (!warnEl) return;

    if (!ageEl || ageEl.value === '' || !sexEl) { warnEl.style.display = 'none'; return; }

    const ageMonths = parseInt(ageEl.value, 10);
    const sex = sexEl.dataset.optcode; // 'MALE' or 'FEMALE'
    if (isNaN(ageMonths) || ageMonths < 0 || ageMonths > 23) { warnEl.style.display = 'none'; return; }

    // Linear interpolation between reference milestones
    function interp(months) {
      for (var i = 1; i < WT_REFS.length; i++) {
        if (months <= WT_REFS[i][0]) {
          var lo = WT_REFS[i-1], hi = WT_REFS[i];
          var t  = (months - lo[0]) / (hi[0] - lo[0]);
          var idx = sex === 'MALE' ? 1 : 2;
          return [
            lo[idx][0] + t * (hi[idx][0] - lo[idx][0]),
            lo[idx][1] + t * (hi[idx][1] - lo[idx][1]),
          ];
        }
      }
      var idx = sex === 'MALE' ? 1 : 2;
      return WT_REFS[WT_REFS.length - 1][idx];
    }

    var range = interp(ageMonths);
    var minWt = range[0], maxWt = range[1];
    var sexLabel = sex === 'MALE' ? 'boy' : 'girl';

    var outOfRange = [];
    ['Q16A_WT1','Q16B_WT2','Q16C_WT3'].forEach(function (vc) {
      var el = getNumEl(vc);
      if (!el || el.value === '') return;
      var val = parseFloat(el.value);
      if (isNaN(val)) return;
      if (val < minWt || val > maxWt) {
        outOfRange.push(vc.split('_')[1] + ' = ' + val.toFixed(2) + ' kg');
      }
    });

    if (outOfRange.length) {
      warnEl.style.display = '';
      warnEl.innerHTML = '⚠ Weight outside reference range for a ' + ageMonths
        + '-month ' + sexLabel + ' (expected ' + minWt.toFixed(1) + '–' + maxWt.toFixed(1)
        + ' kg, 3rd–97th %ile): ' + outOfRange.join(', ') + '. Please verify before submitting.';
    } else {
      warnEl.style.display = 'none';
    }
  };

  // Re-run weight range check when sex or any weight field changes
  const _wt3El = getNumEl('Q16C_WT3');
  if (_wt1El) _wt1El.addEventListener('change', window.validateWeightRange);
  if (_wt2El) _wt2El.addEventListener('change', window.validateWeightRange);
  if (_wt3El) _wt3El.addEventListener('change', window.validateWeightRange);
  document.querySelectorAll('input[type=radio][data-varcode="Q15_SEX"]')
    .forEach(function (el) { el.addEventListener('change', window.validateWeightRange); });

  // ── Height reference range check (3rd–97th percentile) ──────────────
  // [ageMonths, [boysMin,boysMax], [girlsMin,girlsMax]]
  const HT_REFS = [
    [0,  [46.1,53.7], [45.4,52.9]],
    [6,  [63.3,71.9], [61.2,70.3]],
    [12, [71.0,80.5], [68.9,79.2]],
    [18, [76.9,87.7], [74.9,86.5]],
    [23, [81.0,92.9], [79.3,91.8]],
  ];

  window.validateHeightRange = function () {
    const ageEl  = getNumEl('Q14B_AGE_MONTHS');
    const sexEl  = document.querySelector('input[type=radio][data-varcode="Q15_SEX"]:checked');
    const warnEl = document.getElementById('ht-range-warn');
    if (!warnEl) return;

    if (!ageEl || ageEl.value === '' || !sexEl) { warnEl.style.display = 'none'; return; }

    const ageMonths = parseInt(ageEl.value, 10);
    const sex = sexEl.dataset.optcode;
    if (isNaN(ageMonths) || ageMonths < 0 || ageMonths > 23) { warnEl.style.display = 'none'; return; }

    function interpHt(months) {
      for (var i = 1; i < HT_REFS.length; i++) {
        if (months <= HT_REFS[i][0]) {
          var lo = HT_REFS[i-1], hi = HT_REFS[i];
          var t  = (months - lo[0]) / (hi[0] - lo[0]);
          var idx = sex === 'MALE' ? 1 : 2;
          return [
            lo[idx][0] + t * (hi[idx][0] - lo[idx][0]),
            lo[idx][1] + t * (hi[idx][1] - lo[idx][1]),
          ];
        }
      }
      var idx = sex === 'MALE' ? 1 : 2;
      return HT_REFS[HT_REFS.length - 1][idx];
    }

    var range = interpHt(ageMonths);
    var minHt = range[0], maxHt = range[1];
    var sexLabel = sex === 'MALE' ? 'boy' : 'girl';

    var outOfRange = [];
    ['Q17A_HT1','Q17B_HT2','Q17C_HT3'].forEach(function (vc) {
      var el = getNumEl(vc);
      if (!el || el.value === '') return;
      var val = parseFloat(el.value);
      if (isNaN(val)) return;
      if (val < minHt || val > maxHt) {
        outOfRange.push(vc.split('_')[1] + ' = ' + val.toFixed(1) + ' cm');
      }
    });

    if (outOfRange.length) {
      warnEl.style.display = '';
      warnEl.innerHTML = '⚠ Height outside reference range for a ' + ageMonths
        + '-month ' + sexLabel + ' (expected ' + minHt.toFixed(1) + '–' + maxHt.toFixed(1)
        + ' cm, 3rd–97th %ile): ' + outOfRange.join(', ') + '. Please verify before submitting.';
    } else {
      warnEl.style.display = 'none';
    }
  };

  // Re-run height range check when sex or any height field changes
  const _ht3RangeEl = getNumEl('Q17C_HT3');
  if (_ht1El) _ht1El.addEventListener('change', window.validateHeightRange);
  if (_ht2El) _ht2El.addEventListener('change', window.validateHeightRange);
  if (_ht3RangeEl) _ht3RangeEl.addEventListener('change', window.validateHeightRange);
  document.querySelectorAll('input[type=radio][data-varcode="Q15_SEX"]')
    .forEach(function (el) { el.addEventListener('change', window.validateHeightRange); });
})();

// Apply date constraints once all functions are defined
if (window.validateSurveyDates) validateSurveyDates();
</script>
</body>
</html>
