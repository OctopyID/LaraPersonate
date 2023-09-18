<?php

namespace Octopy\Impersonate\Providers;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Octopy\Impersonate\Http\Middleware\ImpersonateMiddleware;

class ImpersonateServiceProvider extends ServiceProvider
{
    /**
     * @param  Router $router
     * @return void
     */
    public function boot(Router $router) : void
    {
        if (config('impersonate.enabled')) {
            $router->pushMiddlewareToGroup('web', ImpersonateMiddleware::class);

            $this->loadViewsFrom(
                __DIR__ . '/../../resources/views', 'impersonate'
            );

            $this->loadRoutesFrom(
                __DIR__ . '/../../routes/impersonate.php'
            );
        }
    }

    /**
     * @return void
     */
    public function register() : void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/impersonate.php', 'impersonate'
        );
    }
}
