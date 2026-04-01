@extends('layouts.admin')
@section('title', isset($survey->id) ? 'Edit Survey' : 'New Survey')
@section('content')
<div class="card" style="max-width:680px">
  <div class="card-header">
    <span class="card-title">{{ isset($survey->id) ? 'Edit Survey' : 'Create Survey' }}</span>
  </div>
  <form method="POST" action="{{ isset($survey->id) ? route('admin.surveys.update', $survey) : route('admin.surveys.store') }}">
    @csrf
    @if(isset($survey->id)) @method('PUT') @endif
    <div class="form-group">
      <label>Title *</label>
      <input type="text" name="title" value="{{ old('title', $survey->title) }}" required>
      @error('title')<div class="error">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
      <label>Description</label>
      <textarea name="description">{{ old('description', $survey->description) }}</textarea>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          @foreach(['draft','active','closed'] as $s)
            <option value="{{ $s }}" {{ old('status', $survey->status ?? 'draft') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>&nbsp;</label>
        <div class="form-check" style="margin-top:10px">
          <input type="checkbox" id="progress" name="show_progress_bar" value="1" {{ old('show_progress_bar', $survey->show_progress_bar ?? true) ? 'checked' : '' }}>
          <label for="progress" style="text-transform:none;font-size:13px;font-weight:400;margin:0">Show progress bar</label>
        </div>
        <div class="form-check" style="margin-top:8px">
          <input type="checkbox" id="partial" name="allow_partial_save" value="1" {{ old('allow_partial_save', $survey->allow_partial_save ?? false) ? 'checked' : '' }}>
          <label for="partial" style="text-transform:none;font-size:13px;font-weight:400;margin:0">Allow partial save</label>
        </div>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Start Date (optional)</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $survey->starts_at?->format('Y-m-d\TH:i')) }}">
      </div>
      <div class="form-group">
        <label>End Date (optional)</label>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $survey->ends_at?->format('Y-m-d\TH:i')) }}">
      </div>
    </div>
    <div style="display:flex;gap:10px">
      <button type="submit" class="btn btn-primary">{{ isset($survey->id) ? 'Save Changes' : 'Create Survey' }}</button>
      <a href="{{ route('admin.surveys.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
