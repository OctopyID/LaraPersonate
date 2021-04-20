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
}
