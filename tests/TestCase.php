<?php

namespace Octopy\LaraPersonate\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Octopy\LaraPersonate\ImpersonateServiceProvider;

/**
 * Class TestCase
 * @package Octopy\LaraPersonate
 */
class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();
    }

    protected function defineDatabaseMigrations()
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
