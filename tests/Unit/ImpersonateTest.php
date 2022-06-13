<?php

namespace Octopy\Impersonate\Tests\Unit;

use Illuminate\Support\Facades\Auth;
use Octopy\Impersonate\Exceptions\ImpersonateException;
use Octopy\Impersonate\ImpersonateManager;
use Octopy\Impersonate\Tests\Models\User;
use Octopy\Impersonate\Tests\TestCase;

class ImpersonateTest extends TestCase
{
    /**
     * @return void
     */
    public function testItCanGetVersion() : void
    {
        $this->assertEquals(ImpersonateManager::VERSION, $this->impersonate->version());
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
        ])
            ->refresh();

        $bar = User::create([
            'name'  => 'Bar',
            'email' => 'bar@baz.qux',
        ])
            ->refresh();

        // first, we need to login as foo
        $this
            ->actingAs($foo)
            ->assertEquals($foo->toArray(), $this->impersonate->getCurrentUser()->toArray());

        // then, try to impersonate bar
        $foo->impersonate($bar);
        $this->assertEquals($bar->toArray(), $this->impersonate->getCurrentUser()->toArray());

        $this->assertEquals($foo->toArray(), $this->impersonate->getImpersonator()->toArray());
        $this->assertEquals($bar->toArray(), $this->impersonate->getImpersonated()->toArray());
    }

    /**
     * @throws ImpersonateException
     */
    public function testItCanImpersonateAnotherUserUsingId() : void
    {
        $foo = User::create([
            'name'  => 'Foo',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ])
            ->refresh();

        $bar = User::create([
            'name'  => 'Bar',
            'email' => 'bar@baz.qux',
        ])
            ->refresh();

        // first, we need to login as foo
        $this
            ->actingAs($foo)
            ->assertEquals($foo->toArray(), $this->impersonate->getCurrentUser()->toArray());

        // then, try to impersonate bar
        $this->impersonate->take(1, 2);
        $this->assertEquals($bar->toArray(), $this->impersonate->getCurrentUser()->toArray());

        $this->assertEquals($foo->toArray(), $this->impersonate->getImpersonator()->toArray());
        $this->assertEquals($bar->toArray(), $this->impersonate->getImpersonated()->toArray());
    }

    /**
     * @return void
     * @throws ImpersonateException
     */
    public function testItCanImpersonateAnotherUserViaImpersonatedUser() : void
    {
        $foo = User::create([
            'name'  => 'Foo',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        $bar = User::create([
            'name'  => 'Bar',
            'email' => 'bar@baz.qux',
            'admin' => false,
        ]);

        $baz = User::create([
            'name'  => 'Baz',
            'email' => 'baz@bar.foo',
            'admin' => false,
        ]);

        // first, we need to login as foo
        $this
            ->actingAs($foo)
            ->assertEquals($foo->toArray(), $this->impersonate->getCurrentUser()->toArray());

        // then, try to impersonate bar
        $foo->impersonate($bar);
        $this->assertEquals($bar->toArray(), $this->impersonate->getCurrentUser()->toArray());

        // then, try to impersonate baz

        $bar->impersonate($baz);
        $this->assertEquals($baz->toArray(), $this->impersonate->getCurrentUser()->toArray());
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

        $this->actingAs($foo);

        $foo->impersonate($bar);

        $this->assertEquals($bar->toArray(), $this->impersonate->getCurrentUser()->toArray());

        $foo->impersonate->leave();

        $this->assertEquals($foo->toArray(), $this->impersonate->getCurrentUser()->toArray());

        $this->assertFalse($this->impersonate->storage()->isInImpersonatingMode());
    }

    /**
     * @return void
     */
    public function testThrowExceptionWhenNotAuthenticated() : void
    {
        $this->expectException(ImpersonateException::class);
        $this->expectExceptionMessage('You must be logged in to impersonate.');

        $foo = User::create([
            'name'  => 'Foo',
            'email' => 'foo@bar.baz',
        ]);

        $foo->impersonate($foo);
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

        $this->actingAs($foo);

        $foo->impersonate($bar);
    }

    /**
     * @return void
     */
    public function testThrowExceptionWhenTargetUserCannotBeImpersonated() : void
    {
        $this->expectException(ImpersonateException::class);
        $this->expectExceptionMessage('You can\'t impersonate this user.');

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

        $this->actingAs($foo);

        $foo->impersonate($bar);
    }
}
