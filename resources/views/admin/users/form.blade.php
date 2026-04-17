@extends('layouts.admin')
@section('title', isset($user->id) ? 'Edit User' : 'New User')
@section('content')
<div class="card" style="max-width:500px">
  <div class="card-header"><span class="card-title">{{ isset($user->id) ? 'Edit User' : 'Create User' }}</span></div>
  <form method="POST" action="{{ isset($user->id) ? route('admin.users.update', $user) : route('admin.users.store') }}">
    @csrf @if(isset($user->id)) @method('PUT') @endif
    <div class="form-group">
      <label>Full Name</label>
      <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
      @error('name')<div style="color:#dc3545;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
      @error('email')<div style="color:#dc3545;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
      <label>Password {{ isset($user->id) ? '(leave blank to keep current)' : '' }}</label>
      <input type="password" name="password" {{ isset($user->id) ? '' : 'required' }}>
    </div>
    <div class="form-group">
      <label>Confirm Password</label>
      <input type="password" name="password_confirmation">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Role</label>
        <select name="role">
          <option value="researcher"   {{ old('role', $user->role) === 'researcher'   ? 'selected' : '' }}>Researcher</option>
          <option value="interviewer"  {{ old('role', $user->role) === 'interviewer'  ? 'selected' : '' }}>Interviewer</option>
          <option value="supervisor"    {{ old('role', $user->role) === 'supervisor'    ? 'selected' : '' }}>Supervisor</option>
          <option value="study_leader" {{ old('role', $user->role) === 'study_leader' ? 'selected' : '' }}>Study Leader</option>
          <option value="admin"        {{ old('role', $user->role) === 'admin'        ? 'selected' : '' }}>Admin</option>
        </select>
      </div>
      <div class="form-group">
        <label>Status</label>
        <div class="form-check" style="margin-top:10px">
          <input type="checkbox" id="active" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
          <label for="active" style="text-transform:none;font-size:13px;font-weight:400;margin:0">Account is active</label>
        </div>
      </div>
    </div>
    <div style="display:flex;gap:10px">
      <button type="submit" class="btn btn-primary">{{ isset($user->id) ? 'Save Changes' : 'Create User' }}</button>
      <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
