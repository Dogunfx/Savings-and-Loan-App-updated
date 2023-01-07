<?php

namespace Modules\Portal\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckClientSession
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if (Auth::check() && Auth::user()->hasRole('client') && empty(session('client_id'))) {
            Auth::logout();
            return redirect('login');
        }
        return $next($request);
    }
}
