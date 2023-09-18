<?php

namespace Octopy\Impersonate\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Octopy\Impersonate\Http\ResponseModifier;
use Octopy\Impersonate\Impersonate;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImpersonateMiddleware
{
    /**
     * @var array|string[]
     */
    protected array $excepted = [
        '_impersonate/*',
    ];

    /**
     * @var ResponseModifier
     */
    protected ResponseModifier $response;

    /**
     * @param  Impersonate $impersonate
     */
    public function __construct(protected Impersonate $impersonate)
    {
        $this->response = new ResponseModifier;
    }

    /**
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) : mixed
    {
        $response = $next($request);

        if ($this->excepted($request) || $this->excluded($response)) {
            return $response;
        }

        if ($this->impersonate->enabled()) {
            $response = $this->response->modify($response);
        }

        return $response;
    }

    /**
     * @param  Request $request
     * @return bool
     */
    private function excepted(Request $request) : bool
    {
        // If request is ajax or wants json, then it is excluded.
        if ($request->ajax() || $request->wantsJson()) {
            return true;
        }

        // And try to match the request path against the excepted.
        foreach ($this->excepted as $excepted) {
            if ($request->is($excepted)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  Response $response
     * @return bool
     */
    private function excluded(Response $response) : bool
    {
        //  We only want to modify the response if it's a successful response.
        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            return true;
        }

        // Also, we don't want to modify the response if it's a json, binary or streamed response.
        // We just want to modify the response if it's a view contains html tags.
        return
            $response instanceof JsonResponse ||
            $response instanceof StreamedResponse ||
            $response instanceof RedirectResponse ||
            $response instanceof BinaryFileResponse ||
            ! preg_match('/<[^<]+>/', $response->getContent());
    }
}
