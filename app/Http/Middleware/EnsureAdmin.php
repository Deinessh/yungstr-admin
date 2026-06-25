<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('admin')->check() || ! Auth::guard('admin')->user()->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
