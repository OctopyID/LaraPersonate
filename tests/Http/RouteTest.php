<?php

namespace Octopy\LaraPersonate\Tests\Http;

use Octopy\LaraPersonate\Tests\TestCase;
use Octopy\LaraPersonate\Http\Controllers\ImpersonateController;

/**
 * Class RouteTest
 * @package Octopy\LaraPersonate\Tests\Http
 */
class RouteTest extends TestCase
{
    /**
     * @var
     */
    protected $router;

    /**
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->router = $this->app['router']->getRoutes();
    }

    /**
     * @return void
     */
    public function testRoutesIsRegistered()
    {
        $this->assertTrue((bool) $this->router->getByName('impersonate.signin'));
        $this->assertTrue((bool) $this->router->getByName('impersonate.logout'));

        $this->assertTrue((bool) $this->router->getByAction(ImpersonateController::class . '@signin'));
        $this->assertTrue((bool) $this->router->getByAction(ImpersonateController::class . '@logout'));
    }
}
