<?php

namespace Octopy\Impersonate\Tests\Unit;

use Illuminate\Support\Facades\Auth;
use Octopy\Impersonate\Exceptions\ImpersonateException;
use Octopy\Impersonate\Impersonate;
use Octopy\Impersonate\Tests\Models\User;
use Octopy\Impersonate\Tests\TestCase;

class ImpersonateTest extends TestCase
{
    /**
     * @var Impersonate
     */
    protected Impersonate $impersonate;

    /**
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();

        $this->impersonate = $this->app->make('impersonate');
    }

    /**
     * @return void
     * @throws ImpersonateException
     */
    public function testItCanImpersonateAnotherUser() : void
    {
        $foo = User::create([
            'name'  => 'Foo',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        $bar = User::create([
            'name'  => 'Bar',
            'email' => 'bar@baz.qux',
        ]);

        // first, we need to login as foo
        $this
            ->actingAs($foo)
            ->assertEquals($foo->toArray(), $this->impersonate->getCurrentUser()->toArray());

        // then, try to impersonate bar
        $foo->impersonate($bar);
        $this->assertEquals($bar->toArray(), $this->impersonate->getCurrentUser()->toArray());
    }

    /**
     * @return void
     */
    public function testItCanLeaveImpersonation() : void
    {
        $foo = User::create([
            'name'  => 'Foo',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        $bar = User::create([
            'name'  => 'Bar',
            'email' => 'bar@baz.qux',
        ]);

        $foo->impersonate($bar);

        $this->assertEquals($bar->toArray(), $this->impersonate->getCurrentUser()->toArray());

        $foo->impersonate()->leave();

        $this->assertEquals($foo->toArray(), $this->impersonate->getCurrentUser()->toArray());

        $this->assertFalse($this->impersonate->storage()->isInImpersonatingMode());
    }

    /**
     * @return void
     */
    public function testThrowExceptionWhenImpersonatingYourSelf() : void
    {
        $this->expectException(ImpersonateException::class);
        $this->expectExceptionMessage('You cannot impersonate yourself.');

        $foo = User::create([
            'name'  => 'Foo',
            'email' => 'foo@bar.baz',
        ]);

        $this
            ->actingAs($foo)
            ->assertEquals($foo->toArray(), $this->impersonate->getCurrentUser()->toArray());

        $foo->impersonate($foo);
    }

    /**
     * @return void
     */
    public function testThrowExceptionWhenUserNoAbilityTryingToImpersonate() : void
    {
        $this->expectException(ImpersonateException::class);
        $this->expectExceptionMessage('You don\'t have the ability to impersonate.');

        $this->impersonate->criteria(
            fn(User $impersonator) => $impersonator->admin,
            fn(User $impersonated) => ! $impersonated->admin
        );

        $foo = User::create([
            'name'  => 'Foo',
            'email' => 'foo@bar.baz',
            'admin' => false,
        ]);

        $bar = User::create([
            'name'  => 'Bar',
            'email' => 'bar@baz.qux',
            'admin' => false,
        ]);

        $foo->impersonate($bar);
    }

    /**
     * @return void
     */
    public function testThrowExceptionWhenTargetUserCannotBeImpersonated() : void
    {
        $this->expectException(ImpersonateException::class);
        $this->expectExceptionMessage('You can\'t impersonate this user.');

        $this->impersonate->criteria(
            fn(User $impersonator) : bool => $impersonator->admin,
            fn(User $impersonated) : bool => ! $impersonated->admin
        );

        $foo = User::create([
            'name'  => 'Foo',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        $bar = User::create([
            'name'  => 'Bar',
            'email' => 'bar@baz.qux',
            'admin' => true,
        ]);

        $foo->impersonate($bar);
    }
}
