<?php

namespace Octopy\LaraPersonate;

use Illuminate\Support\ServiceProvider;

/**
 * Class ImpersonateServiceProvider
 * @package Octopy\LaraPersonate
 */
class ImpersonateServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/impersonate.php', 'impersonate'
        );

        if (! config('impersonate.enabled')) {
            return;
        }

        $this->app->singleton(Impersonate::class, function () {
            return new Impersonate;
        });
    }

    /**
     * @return void
     */
    /**
     * @return void
     */
    public function boot() : void
    {
        if (! config('impersonate.enabled')) {
            return;
        }

        $this->registerRoutes();
        $this->registerPublishing();

        $this->loadViewsFrom(
            __DIR__ . '/../resources/views', 'impersonate'
        );
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
                __DIR__ . '/../config/impersonate.php' => config_path('impersonate.php'),
            ], 'impersonate-config');
        }
    }
}
