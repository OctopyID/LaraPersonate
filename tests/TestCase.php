<?php

namespace Octopy\Impersonate\Tests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Octopy\Impersonate\ImpersonateManager;
use Octopy\Impersonate\ImpersonateServiceProvider;
use Octopy\Impersonate\Tests\Models\User;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * @var ImpersonateManager
     */
    protected ImpersonateManager $impersonate;

    /**
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();

        config([
            'impersonate.model' => User::class,
        ]);

        $this->impersonate = $this->app->make('impersonate');
    }

    /**
     * @return void
     */
    protected function defineDatabaseMigrations() : void
    {
        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback', ['--database' => 'testbench'])->run();
        });
    }

    /**
     * @param  Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * @param  Application $app
     * @return array
     */
    protected function getPackageProviders($app) : array
    {
        return [
            ImpersonateServiceProvider::class,
        ];
    }
}
