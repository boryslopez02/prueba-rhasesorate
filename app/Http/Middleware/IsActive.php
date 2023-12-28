<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsActive
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_active == 1) {
            return $next($request);
        }

        Auth::logout();

        return redirect()->route('login')->with('status', 'Your account is disabled.');
    }
}
