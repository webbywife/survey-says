<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>{{ $survey->title }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Lato',sans-serif;background:#f5f5f7;min-height:100vh;color:#222}
    .s-header{background:#550D0E;color:#fff;padding:16px 0}
    .s-header .inner{max-width:740px;margin:0 auto;padding:0 20px;display:flex;align-items:center;gap:12px}
    .s-seal{width:32px;height:32px;background:#C9A84C;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-weight:700;color:#550D0E;flex-shrink:0}
    .s-title{font-family:'Playfair Display',serif;font-size:17px}
    .prog-wrap{background:#e0e0e0;height:4px}
    .prog-bar{background:#C9A84C;height:4px;transition:width .3s}
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
    input[type=text],input[type=number],input[type=date],input[type=time],textarea{width:100%;padding:10px 13px;border:1px solid #ddd;border-radius:6px;font-size:14px;font-family:inherit;outline:none;transition:border-color .15s}
    input:focus,textarea:focus{border-color:#7B1213}
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
    .skip-hide{display:none!important}
  </style>
</head>
<body>
<div class="s-header">
  <div class="inner">
    <div class="s-seal">S</div>
    <div class="s-title">{{ $survey->title }}</div>
  </div>
</div>
@if($survey->show_progress_bar)
<div class="prog-wrap"><div class="prog-bar" id="prog" style="width:0%"></div></div>
@endif

<div class="s-body">
  <form method="POST" action="{{ route('survey.submit', $survey->public_token) }}" id="sf">
    @csrf
    @if($errors->any())
      <div style="background:#f8d7da;color:#721c24;padding:12px 16px;border-radius:6px;margin-bottom:16px;font-size:13px">
        {{ $errors->first() }}
      </div>
    @endif
    @php $qNum = 0; @endphp
    @foreach($questions as $q)
      @php $qNum++; $existing = $existingAnswers[$q->id] ?? null; @endphp
      <div class="q-card" id="qc-{{ $q->id }}" data-qid="{{ $q->id }}">
        <div class="q-num">Q{{ $qNum }}@if($q->is_required) <span style="color:#dc3545">*</span>@endif</div>
        <div class="q-text">{!! nl2br(e($q->label)) !!}</div>
        @if($q->help_text)<div class="q-help">{{ $q->help_text }}</div>@endif

        @if($q->type==='single_choice')
          <ul class="choice-list">
            @foreach($q->options as $opt)
              <li><label>
                <input type="radio" name="q_{{ $q->id }}" value="{{ $opt->option_code }}" {{ $existing?->value_text===$opt->option_code?'checked':'' }} onchange="applySkip()">
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
          <input type="number" name="q_{{ $q->id }}" style="max-width:200px;margin-top:12px"
            min="{{ $cfg['min']??'' }}" max="{{ $cfg['max']??'' }}"
            value="{{ $existing?->value_text??'' }}" {{ $q->is_required?'required':'' }}>

        @elseif($q->type==='date')
          <input type="date" name="q_{{ $q->id }}" style="max-width:220px;margin-top:12px"
            value="{{ $existing?->value_text??'' }}" {{ $q->is_required?'required':'' }}>

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
              // Group by region
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
            });
          })();

          function loadCities(qid, provCode, provName, savedCity, savedBrgy){
            if(!provCode) return;
            document.getElementById('ph-prov-name-'+qid).value = provName ||
              document.getElementById('ph-prov-'+qid).options[document.getElementById('ph-prov-'+qid).selectedIndex].text;
            const citySel = document.getElementById('ph-city-'+qid);
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
            });
          }

          function loadBarangays(qid, cityCode, cityName, savedBrgy){
            if(!cityCode) return;
            document.getElementById('ph-city-name-'+qid).value = cityName ||
              document.getElementById('ph-city-'+qid).options[document.getElementById('ph-city-'+qid).selectedIndex].text;
            const brgySel = document.getElementById('ph-brgy-'+qid);
            const brgyTxt = document.getElementById('ph-brgy-txt-'+qid);
            brgySel.innerHTML = '<option value="">Loading…</option>';
            fetch('/api/psgc/barangays/'+encodeURIComponent(cityCode)).then(r=>r.json()).then(barangays=>{
              if(barangays.length === 0){
                brgySel.style.display = 'none';
                brgySel.removeAttribute('name');
                brgyTxt.style.display = '';
                brgyTxt.name = 'q_'+qid+'_barangay';
                brgyTxt.value = savedBrgy||'';
              } else {
                brgySel.style.display = '';
                brgySel.name = 'q_'+qid+'_barangay';
                brgyTxt.style.display = 'none';
                brgyTxt.removeAttribute('name');
                brgySel.innerHTML = '<option value="">— Select Barangay —</option>';
                barangays.forEach(b=>{
                  const opt = document.createElement('option');
                  opt.value = b.name; opt.textContent = b.name;
                  if(b.name === (savedBrgy||'')) opt.selected = true;
                  brgySel.appendChild(opt);
                });
              }
            });
          }
          </script>

        @else
          <textarea name="q_{{ $q->id }}" style="margin-top:12px" {{ $q->is_required?'required':'' }}>{{ $existing?->value_text??'' }}</textarea>
        @endif
      </div>
    @endforeach

    <div class="submit-wrap">
      <button type="submit" class="btn-sub">Submit Survey</button>
    </div>
  </form>
</div>

<script>
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
      // hide questions between source and target
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
  if(p) p.style.width=(vis/allCards.length*100)+'%';
}

function selRating(btn){
  const q=btn.dataset.q;
  document.querySelectorAll(`.r-btn[data-q="${q}"]`).forEach(b=>b.classList.remove('sel'));
  btn.classList.add('sel');
  document.getElementById('rv-'+q).value=btn.dataset.v;
}

document.querySelectorAll('input[type=radio],input[type=checkbox]').forEach(el=>el.addEventListener('change',applySkip));
applySkip();
</script>
</body>
</html>
