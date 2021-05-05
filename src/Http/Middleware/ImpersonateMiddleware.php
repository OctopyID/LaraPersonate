<?php

namespace Octopy\LaraPersonate\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Octopy\LaraPersonate\Impersonate;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class ImpersonateMiddleware
 * @package Octopy\LaraPersonate\Http\Middleware
 */
class ImpersonateMiddleware
{
    /**
     * @var Impersonate
     */
    protected Impersonate $impersonate;

    /**
     * @var string[]
     */
    protected array $except = [
        'impersonate/*',
    ];

    /**
     * ImpersonateMiddleware constructor.
     * @param  Impersonate $impersonate
     */
    public function __construct(Impersonate $impersonate)
    {
        $this->impersonate = $impersonate;
        $this->except = array_merge($this->except, config('impersonate.except', [
            //
        ]));
    }

    /**
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (! $this->impersonate->enabled() || $request->ajax() || $this->excepted($request)) {
            return $response;
        }

        if ($response instanceof JsonResponse || $response instanceof BinaryFileResponse || $response instanceof StreamedResponse) {
            return $response;
        }

        if ($this->impersonate->authorized()) {
            return $this->modify($response);
        }

        return $response;
    }

    /**
     * @param  Request $request
     * @return bool
     */
    protected function excepted(Request $request) : bool
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  Response $response
     * @return Response
     */
    private function modify(Response $response) : Response
    {
        $content = $response->getContent();

        $impersonate = $this->minify($this->impersonate->getView());

        $position = strripos($content, '</body>');
        if ($position !== false) {
            $content = substr($content, 0, $position) . $impersonate . PHP_EOL . substr($content, $position);
        } else {
            $content .= $impersonate;
        }

        return $response->setContent($content);
    }

    /**
     * @param  string $content
     * @return string
     */
    private function minify(string $content) : string
    {
        return preg_replace('/>\s+</m', '><', preg_replace('/\n/', '', $content));
    }
}
