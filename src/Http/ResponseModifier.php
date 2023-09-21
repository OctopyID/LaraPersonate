<?php

namespace Octopy\Impersonate\Http;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Octopy\Impersonate\Impersonate;
use function Octopy\Impersonate\impersonate;

class ResponseModifier
{
    /**
     * @param  Impersonate $impersonate
     */
    public function __construct(protected Impersonate $impersonate)
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

        // if the response contains html body, insert the impersonation view into the body.
        $position = strripos($content, '</body>');

        // @codeCoverageIgnoreStart
        if ($position !== false) {
            $content = substr($content, 0, $position) . $impersonate . PHP_EOL . substr($content, $position);
        } else {
            $content .= $impersonate;
        }

        // @codeCoverageIgnoreEnd

        return $response->setContent($content);
    }

    /**
     * @param  string $content
     * @return string
     */
    private function minify(string $content) : string
    {
        $pattern = [
            '/>[^\S ]+/s'       => '>',     // strip whitespaces after tags, except space
            '/[^\S ]+</s'       => '<',     // strip whitespaces before tags, except space
            '/(\s)+/s'          => '\\1',   // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' => '',      // remove HTML comments
            '/> </'             => '><',    // remove whitespace between tags
        ];

        return preg_replace(array_keys($pattern), array_values($pattern), $content);
    }
}
