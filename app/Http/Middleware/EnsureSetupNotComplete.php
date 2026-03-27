<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class EnsureSetupNotComplete
{
    public function handle(Request $request, Closure $next)
    {
        if (User::count() > 0) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
