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

        $this->app->alias(ImpersonateManager::class, 'impersonate');
        $this->app->singleton(ImpersonateManager::class, function () {
            return new ImpersonateManager;
        });

        $this->app->alias(ImpersonateAuthorization::class, 'impersonate.authorization');
        $this->app->singleton(ImpersonateAuthorization::class, function () {
            return new ImpersonateAuthorization($this->app->make('impersonate'));
        });

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
        if (config('impersonate.enabled', false)) {
            // Register the impersonation routes.
            $this->loadRoutesFrom(
                __DIR__ . '/../routes/impersonate.php'
            );

            // Register the impersonation UI views.
            $this->loadViewsFrom(
                __DIR__ . '/../resources/views', 'impersonate'
            );

            // Register the impersonation middleware.
            $router->pushMiddlewareToGroup('web', ImpersonateMiddleware::class);
        }
    }

    /**
     * @return void
     */
    protected function registerPublishing() : void
    {
        $this->publishes([__DIR__ . '/../public' => public_path('vendor/octopyid/impersonate/')], 'impersonate');
        $this->publishes([__DIR__ . '/../config/impersonate.php' => config_path('impersonate.php')], 'impersonate');
    }
}
