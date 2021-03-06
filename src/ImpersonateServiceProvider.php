<?php

namespace Octopy\LaraPersonate;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Octopy\LaraPersonate\Http\Middleware\ImpersonateMiddleware;

/**
 * Class ImpersonateServiceProvider
 * @package Octopy\LaraPersonate
 */
class ImpersonateServiceProvider extends ServiceProvider
{
    /**
     * @return void
     * @throws BindingResolutionException
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/impersonate.php', 'impersonate'
        );

        if (! config('impersonate.enabled')) {
            return;
        }

        $this->app->make(Kernel::class)->pushMiddleware(ImpersonateMiddleware::class);
    }

    /**
     * @return void
     */
    /**
     * @return void
     */
    public function boot() : void
    {
        $this->registerPublishing();

        if (! config('impersonate.enabled')) {
            return;
        }

        $this->registerViews();
        $this->registerRoutes();
    }

    /**
     * @return void
     */
    private function registerViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'impersonate');
    }

    /**
     * @return void
     */
    private function registerRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    /**
     * @return void
     */
    private function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../public' => public_path('vendor/octopyid/impersonate/'),
            ], 'impersonate');

            $this->publishes([
                __DIR__ . '/../config/impersonate.php' => config_path('impersonate.php'),
            ], 'impersonate');
        }
    }
}
