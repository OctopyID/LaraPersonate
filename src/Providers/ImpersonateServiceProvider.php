<?php

namespace Octopy\Impersonate\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Octopy\Impersonate\Contracts\HasImpersonationUI;
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
        $modelClass = config('impersonate.model');
        if (config('impersonate.enabled') && is_string($modelClass)) {
            $implements = class_implements($modelClass);
            if (is_array($implements) && in_array(HasImpersonationUI::class, $implements)) {
                $router->pushMiddlewareToGroup('web', ImpersonateMiddleware::class);

                $this->loadRoutesFrom(
                    __DIR__ . '/../../routes/impersonate.php',
                );

                $this->loadViewsFrom(
                    __DIR__ . '/../../resources/views', 'impersonate',
                );
            }
        }
    }

    /**
     * @return void
     */
    public function register() : void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/impersonate.php', 'impersonate',
        );

        $this->app->alias(Impersonate::class, 'impersonate');

        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
    }

    /**
     * @return void
     */
    protected function registerPublishing() : void
    {
        $this->publishes([__DIR__ . '/../../public' => public_path('vendor/octopyid/impersonate/')], 'impersonate');
        $this->publishes([__DIR__ . '/../../config/impersonate.php' => config_path('impersonate.php')], 'impersonate');
    }
}
