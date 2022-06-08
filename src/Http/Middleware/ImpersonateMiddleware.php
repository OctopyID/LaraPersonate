<?php

namespace Octopy\Impersonate\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ImpersonateMiddleware
{
    /**
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) : mixed
    {
        return $next($request);
    }
}
