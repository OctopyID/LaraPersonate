<?php

namespace Octopy\Sudo;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Octopy\Sudo\Http\Middleware\SudoMiddleware;

/**
 * Class SudoServiceProvider
 *
 * @package Octopy\Sudo
 */
class SudoServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register() : void
    {
        if (config('sudo.enabled')) {
            $this->app->register(RouteServiceProvider::class);

            $this->app[Kernel::class]->pushMiddleware(SudoMiddleware::class, 'web');
        }
    }

    /**
     * @return void
     */
    public function boot() : void
    {
        $this->publishes([
            __DIR__ . '/../resources/assets/dist' => public_path('vendor/octopyid/sudo/'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/../config/sudo.php' => config_path('sudo.php'),
        ], 'config');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sudo');
    }
}
