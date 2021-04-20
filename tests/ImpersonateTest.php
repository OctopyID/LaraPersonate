<?php

namespace Octopy\LaraPersonate\Tests;

/**
 * Class ImpersonateTest
 * @package Octopy\LaraPersonate\Tests
 */
class ImpersonateTest extends TestCase
{
    /**
     * @return void
     */
    public function testSignin()
    {
        $response = $this->post(route('impersonate.signin'));

        $response->assertOk();
    }

    /**
     * @return void
     */
    public function testLogout()
    {
        $response = $this->post(route('impersonate.logout'));

        $response->assertOk();
    }
}
