<?php

namespace Octopy\Impersonate\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Octopy\Impersonate\Http\ResponseModifier;
use Octopy\Impersonate\ImpersonateManager;
use Octopy\Impersonate\ImpersonateAuthorization;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImpersonateMiddleware
{
    /**
     * @var array|string[]
     */
    protected array $excepted = [
        'impersonate/*',
    ];

    /**
     * @var ResponseModifier
     */
    protected ResponseModifier $modifier;

    /**
     * @param  ImpersonateManager $impersonate
     */
    public function __construct(protected ImpersonateManager $impersonate)
    {
        $this->modifier = new ResponseModifier($impersonate);

        $this->excepted = array_merge($this->excepted, config('impersonate.except', [
            //
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

        if (! $this->impersonate->enabled()) {
            return $response;
        }

        // Check if request or response is excluded.
        if ($this->excepted($request) || $this->excluded($response)) {
            return $response;
        }

        // Modify the response if user can do impersonation.
        if ($this->impersonate->authorized()) {
            return $this->modifier->modify($response);
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
            $response instanceof BinaryFileResponse ||
            ! preg_match('/<[^<]+>/', $response->getContent());
    }
}
