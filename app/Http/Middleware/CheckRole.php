<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle($request, Closure $next, $role)
{
    if ($request->user() && $request->user()->role == $role) {
        return $next($request);
    }
    abort(403, 'Unauthorized');
}
}
