<?php

namespace Octopy\Impersonate;

use Illuminate\Foundation\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Octopy\Impersonate\Http\Middleware\ImpersonateMiddleware;

class ImpersonateServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register() : void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/impersonate.php', 'impersonate'
        );

        $this->app->singleton(Impersonate::class, function () {
            return new Impersonate;
        });

        $this->app->alias(Impersonate::class, 'impersonate');

        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
    }

    /**
     * @param  Router $router
     * @return void
     */
    public function boot(Router $router) : void
    {
        if (config('impersonate.enabled')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/impersonate.php');
            $router->pushMiddlewareToGroup('web', ImpersonateMiddleware::class);
        }
    }

    /**
     * @return void
     */
    protected function registerPublishing() : void
    {
        $this->publishes([__DIR__ . '/../config/impersonate.php' => config_path('impersonate.php')], 'impersonate');
    }
}