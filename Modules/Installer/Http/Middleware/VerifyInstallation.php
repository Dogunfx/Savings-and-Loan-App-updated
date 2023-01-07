<?php

namespace Modules\Installer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyInstallation
{
    protected $except = [
        'install*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if (!config('app.installed') && !$request->is('install*')) {
            return redirect()->to('install');
        }

        if (config('app.installed') && $request->is('install*')
            && !$request->is('install/settings') && !$request->is('install/email_settings')
            && !$request->is('install/complete')
        ) {
            return redirect()->to('/');
        }
        return $next($request);

    }
}
