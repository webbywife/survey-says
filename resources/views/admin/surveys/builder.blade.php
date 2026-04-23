@extends('layouts.admin')
@section('title', 'Builder — ' . $survey->title)
@push('styles')
<style>
.builder-wrap{display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start}
.q-item{background:#fff;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;margin-bottom:10px;display:flex;align-items:flex-start;gap:12px}
.q-drag{color:#ccc;flex-shrink:0;margin-top:2px;cursor:grab;font-size:16px}
.q-body{flex:1;min-width:0}
.q-code{font-size:10px;background:#f0f0f0;padding:2px 7px;border-radius:3px;color:#666;font-family:monospace}
.q-label{font-size:14px;color:#222;margin:5px 0 3px}
.q-type{font-size:10px;color:#888;text-transform:uppercase;letter-spacing:.06em}
.q-actions{display:flex;gap:6px;flex-shrink:0}
.panel{background:#fff;border-radius:8px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.08);position:sticky;top:80px;max-height:calc(100vh - 100px);overflow-y:auto}
.panel-title{font-family:'Playfair Display',serif;font-size:16px;color:var(--maroon-d);margin-bottom:16px}
.opt-item{display:flex;align-items:center;gap:8px;padding:5px 0;border-bottom:1px solid #f0f0f0;font-size:13px}
.opt-code{font-family:monospace;font-size:11px;background:#f5f5f5;padding:1px 5px;border-radius:3px}
.add-row{display:flex;gap:6px;margin-top:8px}
.add-row input{flex:1;padding:6px 9px;border:1px solid #ddd;border-radius:4px;font-size:12px}
.section-divider{background:var(--maroon-d);color:#fff;padding:8px 14px;border-radius:5px;margin:16px 0 8px;font-size:11px;text-transform:uppercase;letter-spacing:.08em;display:flex;align-items:center;justify-content:space-between}
.section-divider button{background:rgba(255,255,255,.15);border:none;color:#fff;border-radius:3px;padding:2px 8px;font-size:10px;cursor:pointer;letter-spacing:0;text-transform:none}
.section-divider button:hover{background:rgba(255,255,255,.3)}
.sec-modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center}
.sec-modal-bg.open{display:flex}
.sec-modal{background:#fff;border-radius:10px;padding:28px;width:100%;max-width:520px;box-shadow:0 8px 32px rgba(0,0,0,.18)}
.sec-modal h3{margin:0 0 18px;font-family:'Playfair Display',serif;color:var(--maroon-d);font-size:18px}
.sec-modal label{display:block;font-size:12px;font-weight:700;color:#555;margin-bottom:5px}
.sec-modal textarea{width:100%;box-sizing:border-box;padding:9px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;font-family:inherit;resize:vertical;margin-bottom:14px}
.sec-modal-footer{display:flex;gap:8px;justify-content:flex-end;margin-top:4px}
.add-section-bar{border:2px dashed #C9A84C;border-radius:5px;padding:10px 14px;text-align:center;margin-bottom:10px;color:#C9A84C;font-size:12px;font-weight:700;cursor:pointer;transition:background .15s}
.add-section-bar:hover{background:#fdf6e8}
.req-toggle{border:none;border-radius:3px;font-size:10px;padding:2px 7px;margin-left:8px;cursor:pointer;font-family:inherit;font-weight:700;letter-spacing:.03em;transition:background .15s,color .15s}
.req-on{background:#fde8e8;color:#dc3545}
.req-off{background:#f0f0f0;color:#aaa}
.req-toggle:hover.req-on{background:#f8c0c0}
.req-toggle:hover.req-off{background:#e0e0e0}
</style>
@endpush
@section('content')
<div style="margin-bottom:16px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
  <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-secondary btn-sm">← Back</a>
  <span style="color:#888;font-size:13px">{{ $survey->questions->count() }} question(s)</span>
  <span class="badge badge-{{ $survey->status }}">{{ ucfirst($survey->status) }}</span>
  @if($survey->status==='draft')
    <form action="{{ route('admin.surveys.activate',$survey) }}" method="POST" style="margin-left:auto">@csrf<button class="btn btn-gold btn-sm">Activate Survey</button></form>
  @endif
</div>

<div class="builder-wrap">
  <div>
    <div class="add-section-bar" onclick="addSectionPrompt()">+ Add Section Divider</div>
    <div id="q-list">
    @php $lastSecId = null; @endphp
    @forelse($survey->questions as $q)
      @if($q->section_id && $q->section_id !== $lastSecId)
        @php $lastSecId = $q->section_id; $sec = $survey->sections->firstWhere('id',$q->section_id); @endphp
        @if($sec)
        <div class="section-divider" id="sec-{{ $sec->id }}">
          <span>{{ $sec->title }}@if($sec->description) — <span style="font-weight:400;opacity:.8">{{ $sec->description }}</span>@endif</span>
          <div style="display:flex;gap:4px">
            <button onclick="editSection({{ $sec->id }},'{{ addslashes($sec->title) }}','{{ addslashes($sec->description ?? '') }}')">Edit</button>
            <button onclick="deleteSection({{ $sec->id }})">Delete</button>
          </div>
        </div>
        @endif
      @endif
      <div class="q-item" data-id="{{ $q->id }}">
        <div class="q-drag">⣿</div>
        <div class="q-body">
          <div><span class="q-code">{{ $q->variable_code }}</span> <span class="q-type" style="margin-left:8px">{{ str_replace('_',' ',$q->type) }}</span><button id="req-btn-{{ $q->id }}" onclick="toggleRequired({{ $q->id }},this)" class="req-toggle {{ $q->is_required ? 'req-on' : 'req-off' }}" title="Toggle required">{{ $q->is_required ? '★ required' : '☆ optional' }}</button></div>
          <div class="q-label">{{ Str::limit($q->label,80) }}</div>
          @if($q->isChoiceType()||$q->isGrid())<div style="font-size:11px;color:#aaa">{{ $q->options->count() }} option(s){{ $q->isGrid()?'·'.$q->gridRows->count().' row(s)':'' }}</div>@endif
        </div>
        <div class="q-actions">
          <button class="btn btn-secondary btn-sm" onclick="editQ({{ $q->id }})">Edit</button>
          <button class="btn btn-danger btn-sm" onclick="delQ({{ $q->id }})">×</button>
        </div>
      </div>
    @empty
      <div style="text-align:center;padding:48px;color:#bbb">
        <div style="font-size:32px;margin-bottom:12px">📋</div>
        <p>No questions yet. Add your first question →</p>
      </div>
    @endforelse
    </div>
  </div>

  <div class="panel">
    <div class="panel-title" id="panel-title">Add Question</div>
    <div class="form-group">
      <label>Variable Code *</label>
      <input type="text" id="f-code" placeholder="e.g. Q1, PROVINCE, AGE" style="font-family:monospace">
      <div style="font-size:11px;color:#999;margin-top:3px">Used as STG column header</div>
    </div>
    <div class="form-group">
      <label>Question Label *</label>
      <textarea id="f-label" rows="2" style="resize:vertical"></textarea>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Type *</label>
        <select id="f-type" onchange="onType()">
          <option value="single_choice">Single Choice</option>
          <option value="multi_select">Multi Select</option>
          <option value="open_text">Open Text</option>
          <option value="rating">Rating Scale</option>
          <option value="number">Number</option>
          <option value="date">Date</option>
          <option value="time">Time</option>
          <option value="grid">Grid / Checklist</option>
          <option value="ph_location">PH Location (Province / City / Barangay)</option>
        </select>
      </div>
      <div class="form-group">
        <label>Required?</label>
        <select id="f-req"><option value="1">Yes</option><option value="0">No</option></select>
      </div>
    </div>
    <div class="form-group">
      <label>Section</label>
      <select id="f-section">
        <option value="">— No section —</option>
        @foreach($survey->sections as $sec)
          <option value="{{ $sec->id }}">{{ $sec->title }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label>Help Text</label>
      <input type="text" id="f-help">
    </div>

    <div id="ph-loc-info" style="display:none;background:#f0f4ff;border:1px solid #c7d7ff;border-radius:5px;padding:10px 12px;margin-bottom:8px;font-size:12px;color:#3355aa">
      <strong>PH Location</strong> — renders 3 cascading dropdowns (Province → City/Municipality → Barangay).
      STG export: <code style="background:#e8edff;padding:1px 4px;border-radius:3px">{code}</code>,
      <code style="background:#e8edff;padding:1px 4px;border-radius:3px">{code}_CITY</code>,
      <code style="background:#e8edff;padding:1px 4px;border-radius:3px">{code}_BRGY</code>.
    </div>

    <div id="opts-section" style="display:none;border-top:1px solid #f0f0f0;padding-top:12px;margin-top:4px">
      <div style="font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:#555;margin-bottom:8px">Answer Options <span id="opts-note" style="font-weight:400;color:#aaa;text-transform:none;letter-spacing:0;font-size:11px"></span></div>
      <div id="opts-list"></div>
      <div class="add-row">
        <input type="text" id="new-opt-code" placeholder="Code" style="max-width:80px;flex:0 0 80px">
        <input type="text" id="new-opt-label" placeholder="Label">
        <button class="btn btn-primary btn-sm" onclick="addOpt()">+</button>
      </div>
    </div>

    <div id="rows-section" style="display:none;border-top:1px solid #f0f0f0;padding-top:12px;margin-top:12px">
      <div style="font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:#555;margin-bottom:8px">Grid Rows</div>
      <div id="rows-list"></div>
      <div class="add-row">
        <input type="text" id="new-row-code" placeholder="Code" style="max-width:80px;flex:0 0 80px">
        <input type="text" id="new-row-label" placeholder="Row label">
        <button class="btn btn-primary btn-sm" onclick="addRow()">+</button>
      </div>
    </div>

    <div style="display:flex;gap:8px;margin-top:18px">
      <button class="btn btn-primary" id="save-btn" onclick="saveQ()">Save Question</button>
      <button class="btn btn-secondary" onclick="reset()">Cancel</button>
    </div>
    <div id="f-err" style="color:#dc3545;font-size:13px;margin-top:8px;display:none"></div>
  </div>
</div>

<!-- Section edit modal -->
<div class="sec-modal-bg" id="sec-modal-bg">
  <div class="sec-modal">
    <h3 id="sec-modal-title">Edit Section</h3>
    <label for="sec-modal-text">Section Heading *</label>
    <textarea id="sec-modal-text" rows="4" placeholder="Section heading text…"></textarea>
    <label for="sec-modal-desc">Description / Introductory Text (optional)</label>
    <textarea id="sec-modal-desc" rows="3" placeholder="Optional sub-text shown below the heading…"></textarea>
    <div id="sec-modal-err" style="color:#dc3545;font-size:12px;margin-bottom:8px;display:none"></div>
    <div class="sec-modal-footer">
      <button class="btn btn-secondary btn-sm" onclick="closeSectionModal()">Cancel</button>
      <button class="btn btn-primary btn-sm" id="sec-modal-save" onclick="saveSectionModal()">Save</button>
    </div>
  </div>
</div>

<script>
const SID = {{ $survey->id }};
const CSRF = document.querySelector('meta[name=csrf-token]').content;
let editId = null, opts = [], rows = [];

@php
$allQData = $survey->questions->map(function($q) {
    return [
        'id'            => $q->id,
        'variable_code' => $q->variable_code,
        'label'         => $q->label,
        'type'          => $q->type,
        'is_required'   => $q->is_required,
        'help_text'     => $q->help_text,
        'section_id'    => $q->section_id,
        'options'       => $q->options->map(function($o) {
            return ['id' => $o->id, 'option_code' => $o->option_code, 'label' => $o->label];
        })->values(),
        'grid_rows'     => $q->gridRows->map(function($r) {
            return ['id' => $r->id, 'row_code' => $r->row_code, 'label' => $r->label];
        })->values(),
    ];
})->values();
@endphp
const allQ = @json($allQData);

function onType(){
  const t=document.getElementById('f-type').value;
  const hasOpts=['single_choice','multi_select','rating','grid'].includes(t);
  document.getElementById('opts-section').style.display=hasOpts?'':'none';
  document.getElementById('rows-section').style.display=t==='grid'?'':'none';
  document.getElementById('opts-note').textContent=t==='grid'?'(columns)':t==='rating'?'(scale points)':'';
  const locInfo=document.getElementById('ph-loc-info');
  if(locInfo) locInfo.style.display=t==='ph_location'?'':'none';
}

function renderOpts(){
  document.getElementById('opts-list').innerHTML=opts.map((o,i)=>`
    <div class="opt-item"><span class="opt-code">${esc(o.option_code)}</span><span style="flex:1">${esc(o.label)}</span>
    ${o.id?`<button class="btn btn-danger btn-sm" onclick="delOpt(${o.id},${i})">×</button>`:''}</div>`).join('');
}
function renderRows(){
  document.getElementById('rows-list').innerHTML=rows.map((r,i)=>`
    <div class="opt-item"><span class="opt-code">${esc(r.row_code)}</span><span style="flex:1">${esc(r.label)}</span>
    ${r.id?`<button class="btn btn-danger btn-sm" onclick="delRow(${r.id},${i})">×</button>`:''}</div>`).join('');
}

async function addOpt(){
  const code=document.getElementById('new-opt-code').value.trim(),label=document.getElementById('new-opt-label').value.trim();
  if(!code||!label)return alert('Code and label required.');
  if(editId){const r=await api(`/admin/surveys/${SID}/questions/${editId}/options`,'POST',{option_code:code,label});opts.push(r);}
  else opts.push({option_code:code,label});
  renderOpts();document.getElementById('new-opt-code').value='';document.getElementById('new-opt-label').value='';
}
async function delOpt(id,i){
  if(!confirm('Delete option?'))return;
  if(editId&&id)await api(`/admin/surveys/${SID}/questions/${editId}/options/${id}`,'DELETE');
  opts.splice(i,1);renderOpts();
}
async function addRow(){
  const code=document.getElementById('new-row-code').value.trim(),label=document.getElementById('new-row-label').value.trim();
  if(!code||!label)return alert('Code and label required.');
  if(editId){const r=await api(`/admin/surveys/${SID}/questions/${editId}/grid-rows`,'POST',{row_code:code,label});rows.push(r);}
  else rows.push({row_code:code,label});
  renderRows();document.getElementById('new-row-code').value='';document.getElementById('new-row-label').value='';
}
async function delRow(id,i){
  if(!confirm('Delete row?'))return;
  if(editId&&id)await api(`/admin/surveys/${SID}/questions/${editId}/grid-rows/${id}`,'DELETE');
  rows.splice(i,1);renderRows();
}

function editQ(id){
  const q=allQ.find(x=>x.id===id);if(!q)return;
  editId=id;opts=[...q.options];rows=[...q.grid_rows];
  document.getElementById('panel-title').textContent='Edit Question';
  document.getElementById('f-code').value=q.variable_code;
  document.getElementById('f-label').value=q.label;
  document.getElementById('f-type').value=q.type;
  document.getElementById('f-req').value=q.is_required?'1':'0';
  document.getElementById('f-help').value=q.help_text||'';
  document.getElementById('f-section').value=q.section_id||'';
  document.getElementById('save-btn').textContent='Save Changes';
  onType();renderOpts();renderRows();
}

async function saveQ(){
  const code=document.getElementById('f-code').value.trim(),label=document.getElementById('f-label').value.trim();
  const type=document.getElementById('f-type').value,req=document.getElementById('f-req').value==='1';
  const help=document.getElementById('f-help').value.trim();
  if(!code||!label){showErr('Variable code and label are required.');return;}
  const sectionId=document.getElementById('f-section').value||null;
  const payload={variable_code:code,label,type,is_required:req,help_text:help,section_id:sectionId};
  try{
    if(editId){
      await api(`/admin/surveys/${SID}/questions/${editId}`,'PUT',payload);
    }else{
      const r=await api(`/admin/surveys/${SID}/questions`,'POST',payload);
      const qid=r.question.id;
      for(const o of opts)await api(`/admin/surveys/${SID}/questions/${qid}/options`,'POST',{option_code:o.option_code,label:o.label});
      for(const row of rows)await api(`/admin/surveys/${SID}/questions/${qid}/grid-rows`,'POST',{row_code:row.row_code,label:row.label});
    }
    location.reload();
  }catch(e){}
}

async function delQ(id){
  if(!confirm('Delete this question and all its answers?'))return;
  await api(`/admin/surveys/${SID}/questions/${id}`,'DELETE');location.reload();
}

function reset(){
  editId=null;opts=[];rows=[];
  document.getElementById('panel-title').textContent='Add Question';
  ['f-code','f-label','f-help'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('f-type').value='single_choice';
  document.getElementById('f-req').value='1';
  document.getElementById('f-section').value='';
  document.getElementById('save-btn').textContent='Save Question';
  document.getElementById('f-err').style.display='none';
  document.getElementById('opts-list').innerHTML='';
  document.getElementById('rows-list').innerHTML='';
  onType();
}

let _secModalId = null;

function openSectionModal(id, title, desc, heading){
  _secModalId = id;
  document.getElementById('sec-modal-title').textContent = heading;
  document.getElementById('sec-modal-text').value = title || '';
  document.getElementById('sec-modal-desc').value = desc || '';
  document.getElementById('sec-modal-err').style.display = 'none';
  document.getElementById('sec-modal-bg').classList.add('open');
  document.getElementById('sec-modal-text').focus();
}

function closeSectionModal(){
  document.getElementById('sec-modal-bg').classList.remove('open');
  _secModalId = null;
}

async function saveSectionModal(){
  const title = document.getElementById('sec-modal-text').value.trim();
  const desc  = document.getElementById('sec-modal-desc').value.trim();
  const errEl = document.getElementById('sec-modal-err');
  if(!title){ errEl.textContent='Heading is required.'; errEl.style.display=''; return; }
  const btn = document.getElementById('sec-modal-save');
  btn.disabled = true; btn.textContent = 'Saving…';
  try {
    if(_secModalId){
      await api(`/admin/surveys/${SID}/sections/${_secModalId}`,'PUT',{title,description:desc});
    } else {
      await api(`/admin/surveys/${SID}/sections`,'POST',{title,description:desc});
    }
    location.reload();
  } catch(e){
    btn.disabled=false; btn.textContent='Save';
    errEl.textContent='Save failed — please try again.'; errEl.style.display='';
  }
}

function addSectionPrompt(){
  openSectionModal(null,'','','Add Section Divider');
}

function editSection(id,title,desc){
  openSectionModal(id,title,desc,'Edit Section');
}

async function deleteSection(id){
  if(!confirm('Delete this section? Questions inside will be unassigned but not deleted.'))return;
  await api(`/admin/surveys/${SID}/sections/${id}`,'DELETE');
  location.reload();
}

async function api(url,method,body){
  const r=await fetch(url,{method,headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},body:body?JSON.stringify(body):undefined});
  if(!r.ok){const e=await r.json().catch(()=>({}));showErr(e.message||JSON.stringify(e.errors||'Error'));throw new Error();}
  return r.json();
}

function showErr(m){const e=document.getElementById('f-err');e.textContent=m;e.style.display='';}

async function toggleRequired(id, btn) {
  const isReq = btn.classList.contains('req-on');
  const newVal = !isReq;
  const q = allQ.find(x => x.id === id);
  if (!q) return;
  try {
    await api(`/admin/surveys/${SID}/questions/${id}`, 'PUT', {
      variable_code: q.variable_code,
      label:         q.label,
      type:          q.type,
      is_required:   newVal,
      help_text:     q.help_text || '',
      section_id:    q.section_id || null,
    });
    q.is_required = newVal;
    btn.classList.toggle('req-on',  newVal);
    btn.classList.toggle('req-off', !newVal);
    btn.textContent = newVal ? '★ required' : '☆ optional';
  } catch(e) {}
}
function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}

onType();

// ── Drag-and-drop reorder ─────────────────────────────────────────────────
const sortScript = document.createElement('script');
sortScript.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js';
sortScript.onload = function() {
  Sortable.create(document.getElementById('q-list'), {
    handle: '.q-drag',
    animation: 150,
    ghostClass: 'q-drag-ghost',
    onEnd: async function() {
      const ids = Array.from(document.querySelectorAll('#q-list .q-item[data-id]'))
                       .map(el => parseInt(el.dataset.id));
      try {
        await api(`/admin/surveys/${SID}/questions/reorder`, 'POST', { ids });
      } catch(e) {}
    }
  });
};
document.head.appendChild(sortScript);

document.getElementById('sec-modal-bg').addEventListener('click', function(e){
  if(e.target === this) closeSectionModal();
});
document.addEventListener('keydown', function(e){
  if(e.key === 'Escape') closeSectionModal();
});
</script>
<style>#q-list .q-drag-ghost{opacity:.4;background:#fdf6e8;border-color:#C9A84C}</style>
@endsection
