<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user status is active
        if (!$user->isActive()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account is not active. Please contact administrator.');
        }

        // Check if user has required role
        if (!$user->hasRole($roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
