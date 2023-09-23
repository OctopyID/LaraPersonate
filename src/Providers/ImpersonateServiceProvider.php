<?php

namespace Octopy\Impersonate\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Octopy\Impersonate\Authorization;
use Octopy\Impersonate\Http\Middleware\ImpersonateMiddleware;
use Octopy\Impersonate\Impersonate;

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

            $this->loadRoutesFrom(
                __DIR__ . '/../../routes/impersonate.php'
            );

            $this->loadViewsFrom(
                __DIR__ . '/../../resources/views', 'impersonate'
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

        $this->app->alias(Impersonate::class, 'impersonate');

        $this->app->alias(Authorization::class, 'impersonate.authorization');
        $this->app->singleton(Authorization::class, function () {
            return new Authorization;
        });
    }
}
