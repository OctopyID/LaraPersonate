<?php

namespace Octopy\Impersonate\Http;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Octopy\Impersonate\Impersonate;

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
        if (! is_string($content)) {
            return $response;
        }

        /** @var view-string $viewName */
        $viewName = 'impersonate::impersonate';

        /** @var \Illuminate\View\View $view */
        $view = view($viewName, [
            'impersonate' => $this->impersonate,
        ]);

        $impersonateHtml = $this->minify($view->render());

        // if the response contains html body, insert the impersonation view into the body.
        $position = strripos($content, '</body>');

        // @codeCoverageIgnoreStart
        if ($position !== false) {
            $content = substr($content, 0, $position) . $impersonateHtml . PHP_EOL . substr($content, $position);
        } else {
            $content .= $impersonateHtml;
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

        $result = preg_replace(array_keys($pattern), array_values($pattern), $content);

        return is_string($result) ? $result : $content;
    }
}
