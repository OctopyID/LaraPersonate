<?php

namespace Octopy\Impersonate\Tests\Unit;

use Octopy\Impersonate\Exceptions\ImpersonateException;
use Octopy\Impersonate\Tests\Models\User;
use Octopy\Impersonate\Tests\TestCase;

class ImpersonateAuthorizationTest extends TestCase
{
    /**
     * Testing non-admin user impersonation fail scenario.
     */
    public function testNonAdminCannotImpersonate() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => false,
        ]);

        $bar = User::create([
            'name'  => 'Bar Foo',
            'email' => 'bar@foo.baz',
            'admin' => false,
        ]);

        $this->expectException(ImpersonateException::class);
        $this->expectExceptionMessage('You don\'t have the ability to impersonate.');

        $foo->impersonate($bar);
    }

    /**
     * Testing admin user impersonation fail scenario.
     */
    public function testAdminCannotImpersonateSelf() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        $bar = User::create([
            'name'  => 'Bar Foo',
            'email' => 'bar@foo.baz',
            'admin' => true,
        ]);

        $this->expectException(ImpersonateException::class);
        $this->expectExceptionMessage('You can\'t impersonate this user.');

        $foo->impersonate($bar);
    }
}
