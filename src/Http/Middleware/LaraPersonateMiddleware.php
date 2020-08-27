<?php

namespace Octopy\LaraPersonate\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Octopy\LaraPersonate\LaraPersonate;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class LaraPersonateMiddleware
 *
 * @package Octopy\LaraPersonate\Http\Middleware
 */
class LaraPersonateMiddleware
{
    /**
     * @var LaraPersonate
     */
    protected $personate;

    /**
     * LaraPersonateMiddleware constructor.
     *
     * @param  LaraPersonate  $personate
     */
    public function __construct(LaraPersonate $personate)
    {
        $this->personate = $personate;
    }

    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     * @throws BindingResolutionException
     */
    public function handle($request, Closure $next)
    {
        if ($request->ajax() || ! $this->personate->isEnabled() || ! $this->isAllowed($request) || $this->personate->personateRequest($request)) {
            return $next($request);
        }

        $response = $next($request);

        if ($response instanceof JsonResponse) {
            return $response;
        }

        return $this->personate->modifyResponse($response);
    }

    /**
     * @param  Request  $request
     * @return bool
     */
    private function isAllowed(Request $request) : bool
    {
        if (empty(config('personate.allowed_tld', []))) {
            return true;
        }

        $host = parse_url($request->url(), PHP_URL_HOST);

        if (preg_match('/^localhost|^127\.0\.0/', $host)) {
            return true;
        }

        $fragment = explode('.', $host);
        if (in_array(end($fragment), config('personate.allowed_tld', []), true)) {
            return true;
        }

        return false;
    }
}
