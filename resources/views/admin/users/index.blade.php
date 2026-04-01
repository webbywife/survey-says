@extends('layouts.admin')
@section('title', 'Users')
@section('topbar-actions')
  <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ New User</a>
@endsection
@section('content')
<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Active</th><th>Last Login</th><th></th></tr></thead>
      <tbody>
      @foreach($users as $user)
        <tr>
          <td><strong>{{ $user->name }}</strong></td>
          <td>{{ $user->email }}</td>
          <td><span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
          <td>{{ $user->is_active ? '✓' : '✗' }}</td>
          <td>{{ $user->last_login_at?->format('M d, Y H:i') ?? 'Never' }}</td>
          <td>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary btn-sm">Edit</a>
            @if($user->id !== auth()->id())
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?')">Delete</button>
            </form>
            @endif
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
  <div class="pagination">{{ $users->links() }}</div>
</div>
@endsection
