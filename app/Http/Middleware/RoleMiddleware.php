<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role = 'researcher'): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to continue.');
        }
        if ($role === 'admin' && !auth()->user()->isAdmin()) {
            abort(403, 'Administrator access required.');
        }
        if (!auth()->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }
        return $next($request);
    }
}
