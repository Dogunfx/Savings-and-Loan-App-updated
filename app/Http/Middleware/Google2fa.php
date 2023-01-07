<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class Google2fa
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authenticator = app(Authenticator::class)->boot($request);
        if (Auth::user()->enable_google2fa == 1) {
            if ($authenticator->isAuthenticated()) {
                return $next($request);
            }

            return $authenticator->makeRequestOneTimePasswordResponse();
        }
        return $next($request);
    }
}
