<?php

namespace Octopy\Impersonate\Tests\Unit;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Octopy\Impersonate\Events\BeginImpersonation;
use Octopy\Impersonate\Events\LeaveImpersonation;
use Octopy\Impersonate\Exceptions\ImpersonateException;
use Octopy\Impersonate\Tests\Models\User;
use Octopy\Impersonate\Tests\TestCase;

class ImpersonateTest extends TestCase
{
    /**
     * @return void
     */
    public function testUserImpersonation() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        $bar = User::create([
            'name'  => 'Bar Foo',
            'email' => 'bar@foo.baz',
            'admin' => false,
        ]);

        Auth::login($foo);

        $this->assertTrue($foo->is(
            Auth::user()
        ));

        $this->assertFalse($bar->is(
            Auth::user()
        ));

        $foo->impersonate($bar);

        $this->assertTrue($bar->is(
            Auth::user()
        ));

        $this->assertFalse($foo->is(
            Auth::user()
        ));

        $foo->impersonate()->leave();

        $this->assertTrue($foo->is(
            Auth::user()
        ));

        $this->assertFalse($bar->is(
            Auth::user()
        ));
    }

    /**
     * @throws ImpersonateException
     */
    public function testUserImpersonationEvents() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        $bar = User::create([
            'name'  => 'Bar Foo',
            'email' => 'bar@foo.baz',
            'admin' => false,
        ]);

        Event::fake([
            BeginImpersonation::class,
            LeaveImpersonation::class,
        ]);

        $foo->impersonate($bar)->leave();

        Event::assertDispatched(BeginImpersonation::class);
        Event::assertDispatched(LeaveImpersonation::class);
    }

    /**
     * @return void
     */
    public function testImpersonateSelfThrowsException() : void
    {
        $this->expectException(ImpersonateException::class);
        $this->expectExceptionMessage('You cannot impersonate yourself.');

        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
        ]);

        $foo->impersonate($foo);
    }
}
