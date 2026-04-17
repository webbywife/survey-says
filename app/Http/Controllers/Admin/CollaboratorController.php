<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\Request;

class CollaboratorController extends Controller
{
    public function store(Request $request, Survey $survey)
    {
        $this->authorizeManage($survey);

        $request->validate(['email' => ['required', 'email', 'exists:users,email']]);

        $user = User::where('email', $request->email)->first();

        if ($user->id === $survey->user_id) {
            return back()->with('error', 'That user is already the survey owner.');
        }

        $survey->collaboratorUsers()->syncWithoutDetaching([$user->id]);

        return back()->with('success', "{$user->name} added as collaborator.");
    }

    public function destroy(Survey $survey, User $collaborator)
    {
        $this->authorizeManage($survey);
        $survey->collaboratorUsers()->detach($collaborator->id);
        return back()->with('success', "{$collaborator->name} removed.");
    }

    private function authorizeManage(Survey $survey): void
    {
        if (!$survey->isOwnedOrAdminBy(auth()->user())) abort(403);
    }
}
