<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperadminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('superadmin_id')) {
            return redirect()->route('superadmin.login');
        }

        return $next($request);
    }
}