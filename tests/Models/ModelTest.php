<?php

namespace Octopy\LaraPersonate\Tests\Models;

use Throwable;
use Octopy\LaraPersonate\Tests\TestCase;
use Octopy\LaraPersonate\Tests\Stubs\Models\User;

/**
 * Class ModelTest
 * @package Octopy\LaraPersonate\Tests\Models
 */
class ModelTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();

        config([
            'impersonate.model' => User::class,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function testImpersonateAuthorizationException()
    {
        $this->expectExceptionMessage('User does not have access to impersonate.');
        User::user()->impersonate(User::admin());
    }

    /**
     * @throws Throwable
     */
    public function testUserCantToBeImpersonatedException()
    {
        $this->expectExceptionMessage('User cannot to be impersonated.');
        User::admin()->impersonate(User::super());
    }

    /**
     * @throw UnauthorizedException
     */
    public function testImpersonateAuthorizationExceptionById()
    {
        config([
            'impersonate.model' => User::class,
        ]);

        $this->expectExceptionMessage('User does not have access to impersonate.');
        User::user()->impersonate(2);
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
        User::admin()->impersonate(1);
    }

    /**
     * @return void
     */
    public function testImpersonate()
    {
        $real = User::user();

        $fake = User::admin()->impersonate($real);
        $this->assertEquals($real->email, $fake->email);

        $fake = User::super()->impersonate($real);
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
}
