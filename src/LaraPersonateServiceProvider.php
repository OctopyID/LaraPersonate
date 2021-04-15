<?php

namespace Octopy\LaraPersonate;

use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Octopy\LaraPersonate\Http\Middleware\LaraPersonateMiddleware;

/**
 * Class LaraPersonateServiceProvider
 *
 * @package Octopy\LaraPersonate
 */
class LaraPersonateServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register() : void
    {
        if (config('impersonate.enabled', false)) {
            $this->app[Kernel::class]->pushMiddleware(LaraPersonateMiddleware::class, 'web');

            Route::group(['prefix' => '_impersonate', 'namespace' => 'Octopy\LaraPersonate\Http\Controllers', 'middleware' => 'web'], static function () {
                Route::get('get-users', 'LaraPersonateController@getUsers')->name('impersonate.users');
                Route::post('try-signin', 'LaraPersonateController@trySignin')->name('impersonate.signin');
                Route::get('try-signout', 'LaraPersonateController@trySignout')->name('impersonate.signout');
            });
        }
    }

    /**
     * @return void
     */
    public function boot() : void
    {
        $this->publishes([
            __DIR__ . '/../resources/assets/dist' => public_path('vendor/supianidz/impersonate/'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/../config/impersonate.php' => config_path('impersonate.php'),
        ], 'config');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'impersonate');
    }
}
