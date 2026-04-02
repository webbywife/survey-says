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
.section-divider{background:var(--maroon-d);color:#fff;padding:8px 14px;border-radius:5px;margin:16px 0 8px;font-size:11px;text-transform:uppercase;letter-spacing:.08em}
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
    <div id="q-list">
    @forelse($survey->questions as $q)
      <div class="q-item" data-id="{{ $q->id }}">
        <div class="q-drag">⣿</div>
        <div class="q-body">
          <div><span class="q-code">{{ $q->variable_code }}</span> <span class="q-type" style="margin-left:8px">{{ str_replace('_',' ',$q->type) }}</span>@if($q->is_required)<span style="color:#dc3545;font-size:11px;margin-left:6px">req</span>@endif</div>
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
        </select>
      </div>
      <div class="form-group">
        <label>Required?</label>
        <select id="f-req"><option value="1">Yes</option><option value="0">No</option></select>
      </div>
    </div>
    <div class="form-group">
      <label>Help Text</label>
      <input type="text" id="f-help">
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
  document.getElementById('save-btn').textContent='Save Changes';
  onType();renderOpts();renderRows();
}

async function saveQ(){
  const code=document.getElementById('f-code').value.trim(),label=document.getElementById('f-label').value.trim();
  const type=document.getElementById('f-type').value,req=document.getElementById('f-req').value==='1';
  const help=document.getElementById('f-help').value.trim();
  if(!code||!label){showErr('Variable code and label are required.');return;}
  const payload={variable_code:code,label,type,is_required:req,help_text:help};
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
  document.getElementById('save-btn').textContent='Save Question';
  document.getElementById('f-err').style.display='none';
  document.getElementById('opts-list').innerHTML='';
  document.getElementById('rows-list').innerHTML='';
  onType();
}

async function api(url,method,body){
  const r=await fetch(url,{method,headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},body:body?JSON.stringify(body):undefined});
  if(!r.ok){const e=await r.json().catch(()=>({}));showErr(e.message||JSON.stringify(e.errors||'Error'));throw new Error();}
  return r.json();
}

function showErr(m){const e=document.getElementById('f-err');e.textContent=m;e.style.display='';}
function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}

onType();
</script>
@endsection
