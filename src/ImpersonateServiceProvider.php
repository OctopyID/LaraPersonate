<?php

namespace Octopy\Impersonate;

use Illuminate\Support\ServiceProvider;

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

        $this->app->singleton('impersonate', function () {
            return new Impersonate();
        });
    }
}
