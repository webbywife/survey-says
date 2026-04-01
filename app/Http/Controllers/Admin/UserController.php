<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['password'] = bcrypt($data['password']);
        User::create($data);
        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user->id, false);
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) return back()->with('error', 'Cannot delete your own account.');
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    private function validated(Request $request, ?int $ignoreId = null, bool $passwordRequired = true): array
    {
        return $request->validate([
            'name'      => 'required|string|max:150',
            'email'     => 'required|email|unique:users,email,' . $ignoreId,
            'password'  => ($passwordRequired ? 'required' : 'nullable') . '|min:8|confirmed',
            'role'      => 'required|in:admin,researcher',
            'is_active' => 'nullable|boolean',
        ]);
    }
}
