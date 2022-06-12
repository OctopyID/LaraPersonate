<?php

namespace Octopy\Impersonate\Http;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Octopy\Impersonate\ImpersonateManager;

class ResponseModifier
{
    /**
     * @param  ImpersonateManager $impersonate
     */
    public function __construct(protected ImpersonateManager $impersonate)
    {
        //
    }

    /**
     * @param  Response $response
     * @return Response
     */
    public function modify(Response $response) : Response
    {
        $content = $response->getContent();

        $impersonate = $this->minify(view('impersonate::impersonate', [
            'impersonate' => $this->impersonate,
        ]));

        // If the response contains html body, insert the impersonation view into the body.
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
