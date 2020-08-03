<?php

namespace Octopy\Sudo\Http\Middleware;

use Closure;
use Octopy\Sudo\Sudo;
use Illuminate\Http\Request;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class SudoMiddleware
 *
 * @package Octopy\Sudo\Http\Middlewares
 */
class SudoMiddleware
{
    /**
     * @var Sudo
     */
    protected $sudo;

    /**
     * @var string[]
     */
    protected $except = [
        'octopyid/sudo/signin',
    ];

    /**
     * SudoMiddleware constructor.
     *
     * @param  Sudo  $sudo
     */
    public function __construct(Sudo $sudo)
    {
        $this->sudo = $sudo;
    }

    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     * @throws BindingResolutionException
     */
    public function handle($request, Closure $next)
    {
        if ($request->ajax() || ! $request->isMethod('GET')) {
            return $next($request);
        }

        if (! $this->sudo->isEnabled() || $this->isExcepted($request)) {
            return $next($request);
        }

        return $this->sudo->modifyResponse($request, $next($request));
    }

    /**
     * @param  Request  $request
     * @return bool
     */
    private function isExcepted(Request $request) : bool
    {
        if (config('sudo.allowed_tld') === '*') {
            return false;
        }

        $host = parse_url($request->url(), PHP_URL_HOST);

        if (preg_match('/^localhost|^127\.0\.0/', $host)) {
            return false;
        }

        $fragment = explode('.', $host);
        if (in_array(end($fragment), config('sudo.allowed_tld', []), true)) {
            return false;
        }

        foreach ($this->except as $except) {
            if ($request->is($except)) {
                return false;
            }
        }

        return true;
    }
}
