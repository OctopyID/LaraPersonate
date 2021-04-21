<?php

namespace Octopy\LaraPersonate\Tests;

use Throwable;
use Illuminate\Support\Facades\App;
use Octopy\LaraPersonate\Impersonate;
use Octopy\LaraPersonate\Tests\Stubs\Models\User;

/**
 * Class ImpersonateTest
 * @package Octopy\LaraPersonate\Tests
 */
class ImpersonateTest extends TestCase
{
    /**
     * @var Impersonate
     */
    protected Impersonate $impersonate;

    /**
     * @return void
     */
    public function testWhoCanImpersonate()
    {
        $this->assertTrue(User::admin()->canImpersonate());
        $this->assertTrue(User::super()->canImpersonate());
    }

    /**
     * @return void
     */
    public function testWhoCantImpersonate()
    {
        $this->assertFalse(User::user()->canImpersonate());
    }

    /**
     * @return void
     */
    public function testWhoCanBeImpersonated()
    {
        $this->assertTrue(User::user()->canBeImpersonated());
        $this->assertTrue(User::admin()->canBeImpersonated());
    }

    /**
     * @return void
     */
    public function testWhoCantBeImpersonated()
    {
        $this->assertFalse(User::super()->canBeImpersonated());
    }

    /**
     * @throws Throwable
     */
    public function testImpersonateAuthorizationException()
    {
        $this->expectExceptionMessage('User does not have access to impersonate.');
        $this->impersonate->take(User::user(), User::admin());
    }

    /**
     * @throws Throwable
     */
    public function testUserCantToBeImpersonatedException()
    {
        $this->expectExceptionMessage('User cannot to be impersonated.');
        $this->impersonate->take(User::admin(), User::super());
    }

    /**
     * @throws Throwable
     */
    public function testImpersonateAuthorizationExceptionById()
    {
        config([
            'impersonate.model' => User::class,
        ]);

        $this->expectExceptionMessage('User does not have access to impersonate.');
        $this->impersonate->take(3, 2);
    }

    /**
     * @throws Throwable
     */
    public function testUserCantToBeImpersonatedExceptionById()
    {
        config([
            'impersonate.model' => User::class,
        ]);

        $this->expectExceptionMessage('User cannot to be impersonated.');
        $this->impersonate->take(2, 1);
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function testImpersonate()
    {
        $real = User::user();

        $fake = $this->impersonate->take(User::admin(), $real);
        $this->assertEquals($real->email, $fake->email);

        $fake = $this->impersonate->take(User::super(), $real);
        $this->assertEquals($real->email, $fake->email);
    }

    /**
     * @return void
     */
    public function testImpersonateById()
    {
        config([
            'impersonate.model' => User::class,
        ]);

        $real = User::user();

        $fake = User::admin()->impersonate($real->id);
        $this->assertEquals($real->email, $fake->email);

        $fake = User::super()->impersonate($real->id);
        $this->assertEquals($real->email, $fake->email);
    }

    /**
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();

        $this->impersonate = App::make(Impersonate::class);
    }
}
