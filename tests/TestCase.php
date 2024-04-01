<?php

namespace Octopy\Impersonate\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Octopy\Impersonate\Impersonate;
use Octopy\Impersonate\Providers\ImpersonateServiceProvider;
use Octopy\Impersonate\Tests\Models\User1;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * @var string
     */
    protected string $database;

    /**
     * @var Impersonate
     */
    protected Impersonate $impersonate;

    /**
     * @param  string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->database = __DIR__ . '/../database/database.sqlite';
    }

    /**
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();

        config([
            'impersonate.model' => User1::class,
        ]);

        $this->impersonate = $this->app->make('impersonate');
    }

    /**
     * @return void
     */
    protected function defineDatabaseMigrations() : void
    {
        if (! file_exists($this->database)) {
            touch($this->database);
        }

        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback', ['--database' => 'testbench'])->run();
            if (file_exists($this->database)) {
                unlink($this->database);
            }
        });
    }

    /**
     * @param  Application $app
     */
    protected function defineEnvironment($app) : void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => __DIR__ . '/../database/database.sqlite',
        ]);
    }

    /**
     * @param  Application $app
     * @return string[]
     */
    protected function getPackageProviders($app) : array
    {
        return [
            ImpersonateServiceProvider::class,
        ];
    }
}
