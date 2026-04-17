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

    public function importForm()
    {
        return view('admin.users.import');
    }

    public function importStore(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:5120']);

        $file   = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = array_map('trim', fgetcsv($handle));

        $required = ['name', 'email', 'password', 'role'];
        $missing  = array_diff($required, $header);
        if ($missing) {
            fclose($handle);
            return back()->with('error', 'Missing columns: ' . implode(', ', $missing));
        }

        $validRoles = ['admin', 'researcher', 'interviewer', 'supervisor', 'study_leader'];
        $created = $skipped = 0;
        $errors  = [];
        $row     = 1;

        while (($line = fgetcsv($handle)) !== false) {
            $row++;
            if (count($line) !== count($header)) {
                $errors[] = "Row {$row}: column count mismatch.";
                continue;
            }
            $data = array_combine($header, array_map('trim', $line));

            if (empty($data['name']) || empty($data['email']) || empty($data['password']) || empty($data['role'])) {
                $errors[] = "Row {$row}: name, email, password, and role are required.";
                continue;
            }
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row {$row}: invalid email "{$data['email']}".";
                continue;
            }
            if (!in_array($data['role'], $validRoles)) {
                $errors[] = "Row {$row}: invalid role "{$data['role']}". Must be one of: " . implode(', ', $validRoles);
                continue;
            }
            if (strlen($data['password']) < 8) {
                $errors[] = "Row {$row}: password must be at least 8 characters.";
                continue;
            }
            if (User::where('email', $data['email'])->exists()) {
                $skipped++;
                continue;
            }

            User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => bcrypt($data['password']),
                'role'      => $data['role'],
                'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
            ]);
            $created++;
        }
        fclose($handle);

        $msg = "{$created} user(s) created, {$skipped} skipped (email already exists).";
        return redirect()->route('admin.users.import')
            ->with('import_result', ['created' => $created, 'skipped' => $skipped, 'errors' => $errors])
            ->with('success', $msg);
    }

    private function validated(Request $request, ?int $ignoreId = null, bool $passwordRequired = true): array
    {
        return $request->validate([
            'name'      => 'required|string|max:150',
            'email'     => 'required|email|unique:users,email,' . $ignoreId,
            'password'  => ($passwordRequired ? 'required' : 'nullable') . '|min:8|confirmed',
            'role'      => 'required|in:admin,researcher,interviewer,supervisor,study_leader',
            'is_active' => 'nullable|boolean',
        ]);
    }
}
