<?php

namespace Octopy\Impersonate\Tests\Unit;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Octopy\Impersonate\Concerns\HasImpersonation;
use Octopy\Impersonate\Events\BeginImpersonation;
use Octopy\Impersonate\Events\LeaveImpersonation;
use Octopy\Impersonate\Exceptions\ImpersonateException;
use Octopy\Impersonate\Tests\Models\User1;
use Octopy\Impersonate\Tests\Models\User2;
use Octopy\Impersonate\Tests\TestCase;
use function Octopy\Impersonate\impersonate;

class ImpersonateTest extends TestCase
{
    /**
     * @return void
     */
    public function testUserImpersonation() : void
    {
        $foo = User1::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        $bar = User1::create([
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
        $foo = User1::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        $bar = User1::create([
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

        $foo = User1::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
        ]);

        $foo->impersonate($foo);
    }

    /**
     * @return void
     */
    public function testModelShouldUsesHasImpersonation() : void
    {
        config([
            'impersonate.model' => User2::class,
        ]);

        $this->expectException(ImpersonateException::class);
        $this->expectExceptionMessage(config('impersonate.model') . ' does not uses ' . HasImpersonation::class);

        $foo = User2::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
        ]);

        impersonate()->begin($foo, $foo);
    }
}
