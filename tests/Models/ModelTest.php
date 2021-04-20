<?php

namespace Octopy\LaraPersonate\Tests\Models;

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
    public function testCanImpersonate()
    {
        $this->assertTrue(User::admin()->canImpersonate());
        $this->assertTrue(User::superAdmin()->canImpersonate());
    }

    /**
     * @return void
     */
    public function testCantImpersonate()
    {
        $this->assertFalse(User::user()->canImpersonate());
    }

    /**
     * @return void
     */
    public function testCanBeImpersonated()
    {
        $this->assertTrue(User::user()->canBeImpersonated());
        $this->assertTrue(User::admin()->canBeImpersonated());
    }

    /**
     * @return void
     */
    public function testCantBeImpersonated()
    {
        $this->assertFalse(User::superAdmin()->canBeImpersonated());
    }

    /**
     * @throw UnauthorizedException
     */
    public function testImpersonateAuthorization()
    {
        $this->expectExceptionMessage('User cannot to be impersonated.');
        User::admin()->impersonate(User::superAdmin());

        $this->expectExceptionMessage('User does not have access to impersonate.');
        User::user()->impersonate(User::admin());
    }

    /**
     * @throw UnauthorizedException
     */
    public function testImpersonateAuthorizationById()
    {
        config([
            'impersonate.model' => User::class,
        ]);

        $this->expectExceptionMessage('User cannot to be impersonated.');
        User::admin()->impersonate(1);

        $this->expectExceptionMessage('User does not have access to impersonate.');
        User::user()->impersonate(1);
    }

    /**
     * @return void
     */
    public function testImpersonate()
    {
        $real = User::user();

        $fake = User::admin()->impersonate($real);
        $this->assertEquals($real->email, $fake->email);

        $fake = User::superAdmin()->impersonate($real);
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

        $fake = User::superAdmin()->impersonate($real->id);
        $this->assertEquals($real->email, $fake->email);
    }
}
