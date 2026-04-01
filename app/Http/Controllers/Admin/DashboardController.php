<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\Response;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $query = $user->isAdmin() ? Survey::query() : Survey::ownedBy($user->id);

        $surveyIds     = (clone $query)->pluck('id');
        $totalSurveys  = (clone $query)->count();
        $activeSurveys = (clone $query)->where('status', 'active')->count();
        $totalResponses = Response::whereIn('survey_id', $surveyIds)->count();
        $recentSurveys = (clone $query)->latest()->limit(5)->with('user')->get();
        $totalUsers    = $user->isAdmin() ? User::count() : null;

        return view('admin.dashboard', compact(
            'totalSurveys', 'activeSurveys', 'totalResponses', 'recentSurveys', 'totalUsers'
        ));
    }
}
