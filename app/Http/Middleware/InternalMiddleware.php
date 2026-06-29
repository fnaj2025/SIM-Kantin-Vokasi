<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/internal/login')->with('error', 'Akses ditolak. Silakan login.');
        }

        if (auth()->user()->role === 'customer') {
            return redirect('/')->with('error', 'Akses internal ditolak.');
        }

        return $next($request);
    }
}
