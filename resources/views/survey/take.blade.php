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
  const btn = document.getElementById('submit-btn');
  if (btn && !btn.disabled) btn.textContent = (forced || !navigator.onLine) ? 'Save Offline' : 'Submit Survey';
  document.getElementById('offline-bar').classList.toggle('show', forced || !navigator.onLine);
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

// ─── Online/offline event listeners ──────────────────────────────────────────
window.addEventListener('online',  () => { setOfflineUI(false); if (!isForceOffline()) syncNow(); });
window.addEventListener('offline', () => setOfflineUI(true));
applyModeUI();
refreshSyncBar();

// ─── Register Service Worker ──────────────────────────────────────────────────
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js')
    .catch(err => console.warn('SW registration failed:', err));
}
</script>
</body>
</html>
