<?php

namespace Octopy\Impersonate\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Octopy\Impersonate\Http\ResponseModifier;
use Octopy\Impersonate\Impersonate;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
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
        $this->response = new ResponseModifier($this->impersonate);
        $this->excepted = array_merge($this->excepted, config('impersonate.except', [

        ]));
    }

    /**
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) : mixed
    {
        $response = $next($request);

        if (! config('impersonate.enabled') || $this->excepted($request) || $this->excluded($response)) {
            return $response;
        }

        if ($this->impersonate->authorized()) {
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
        // if request is ajax or wants json, then it is excluded.
        if ($request->ajax() || $request->wantsJson()) {
            return true;
        }

        // and try to match the request path against the excepted.
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
        // modify the response if it's a successful response.
        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            return true;
        }

        // also, we don't want to modify the response if it's a json, binary or streamed response.
        // we just want to modify the response if it's a view contains html tags.
        return
            $response instanceof JsonResponse || $response instanceof BinaryFileResponse ||
            $response instanceof RedirectResponse || $response instanceof StreamedResponse ||
            ! preg_match('/<[^<]+>/', $response->getContent());
    }
}
