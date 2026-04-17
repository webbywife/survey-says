@extends('layouts.admin')
@section('title', 'Bulk Import Users')
@section('topbar-actions')
  <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">← Users</a>
@endsection
@section('content')

@if(session('success'))
  <div class="flash flash-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="flash flash-error">{{ session('error') }}</div>
@endif

@if($result = session('import_result'))
  @if(count($result['errors']))
    <div class="flash flash-error" style="margin-bottom:16px">
      <strong>{{ count($result['errors']) }} row error(s):</strong>
      <ul style="margin:6px 0 0 18px;line-height:1.8">
        @foreach($result['errors'] as $err)<li>{{ $err }}</li>@endforeach
      </ul>
    </div>
  @endif
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

  {{-- Upload Form --}}
  <div class="card">
    <div class="card-header"><span class="card-title">Upload CSV</span></div>

    <div style="background:#fdf6e8;border:1px solid #e2c47a;border-radius:5px;padding:12px;font-size:13px;color:#7a5c00;margin-bottom:20px">
      <strong>Format:</strong> First row must be column headers matching the names shown on the right.
      Download the template below to get started quickly.
    </div>

    <form action="{{ route('admin.users.import.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="form-group">
        <label>CSV File <span style="color:#dc3545">*</span></label>
        <input type="file" name="csv_file" accept=".csv,text/csv" required
               style="display:block;width:100%;padding:8px;border:1px solid #ddd;border-radius:5px;font-size:13px">
        <p style="font-size:12px;color:#888;margin-top:4px">Max 5 MB · UTF-8 encoded · First row must be column headers</p>
      </div>

      <div style="background:#f8f8f8;border-radius:5px;padding:12px;font-size:13px;margin-bottom:16px">
        <strong>Rules:</strong>
        <ul style="margin:8px 0 0 18px;line-height:1.8">
          <li>Rows where the email already exists will be <strong>skipped</strong> (no duplicates).</li>
          <li>Passwords must be at least 8 characters. They are stored encrypted.</li>
          <li>Valid roles: <code>admin</code>, <code>researcher</code>, <code>interviewer</code>, <code>supervisor</code>, <code>study_leader</code></li>
          <li><code>is_active</code> column is optional — defaults to <code>1</code> (active).</li>
        </ul>
      </div>

      <div style="display:flex;gap:10px;align-items:center">
        <button type="submit" class="btn btn-gold">⬆ Import Users</button>
        <a href="{{ route('admin.users.import') }}?download=template" class="btn btn-secondary btn-sm">↓ Download Template</a>
      </div>
    </form>
  </div>

  {{-- Column Reference --}}
  <div class="card">
    <div class="card-header"><span class="card-title">Required Column Headers</span></div>
    <div style="font-size:13px;line-height:2">
      @php
        $cols = [
          ['name',      'Full name of the user', true],
          ['email',     'Login email address — must be unique', true],
          ['password',  'Plain-text password (min 8 chars) — stored encrypted', true],
          ['role',      'admin / researcher / interviewer / supervisor / study_leader', true],
          ['is_active', '1 = active, 0 = inactive (optional, defaults to 1)', false],
        ];
      @endphp
      @foreach($cols as [$col, $desc, $req])
      <div style="padding:8px 0;border-bottom:1px solid #f8f8f8;display:flex;gap:12px;align-items:flex-start">
        <span style="font-family:monospace;font-size:12px;color:{{ $req ? '#C9A84C' : '#aaa' }};font-weight:{{ $req ? '700' : '400' }};width:90px;flex-shrink:0">{{ $col }}</span>
        <span style="font-size:12px;color:#555;flex:1">{{ $desc }}{{ !$req ? ' <em>(optional)</em>' : '' }}</span>
        @if(!$req)<span style="font-size:10px;color:#aaa;flex-shrink:0">optional</span>@endif
      </div>
      @endforeach
    </div>

    <div style="margin-top:16px;background:#f8f8f8;border-radius:5px;padding:12px">
      <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Example CSV</div>
      <pre style="font-size:11px;margin:0;white-space:pre-wrap;color:#444">name,email,password,role,is_active
Juan dela Cruz,juan@example.com,Password123,interviewer,1
Maria Santos,maria@example.com,Password123,supervisor,1
Pedro Reyes,pedro@example.com,Password123,study_leader,1</pre>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
// Handle template download as client-side CSV
const url = new URL(window.location.href);
if (url.searchParams.get('download') === 'template') {
  const csv = 'name,email,password,role,is_active\n';
  const blob = new Blob([csv], { type: 'text/csv' });
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = 'users_import_template.csv';
  a.click();
  history.replaceState({}, '', url.pathname);
}
</script>
@endpush
