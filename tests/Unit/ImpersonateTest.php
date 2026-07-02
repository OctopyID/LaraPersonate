<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Octopy\Impersonate\Concerns\HasImpersonation;
use Octopy\Impersonate\Events\BeginImpersonation;
use Octopy\Impersonate\Events\LeaveImpersonation;
use Octopy\Impersonate\Exceptions\ImpersonateException;
use Octopy\Impersonate\Tests\Models\User1;
use Octopy\Impersonate\Tests\Models\User2;

use function Octopy\Impersonate\impersonate;

it('can impersonate a user', function () {
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

    expect($foo->is(Auth::user()))->toBeTrue()
        ->and($bar->is(Auth::user()))->toBeFalse();

    $foo->impersonate($bar);

    expect($bar->is(Auth::user()))->toBeTrue()
        ->and($foo->is(Auth::user()))->toBeFalse();

    $foo->impersonate()->leave();

    expect($foo->is(Auth::user()))->toBeTrue()
        ->and($bar->is(Auth::user()))->toBeFalse();
});

it('dispatches impersonation events', function () {
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
});

it('throws exception when impersonating self', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
    ]);

    expect(fn () => $foo->impersonate($foo))
        ->toThrow(ImpersonateException::class, 'You cannot impersonate yourself.');
});

it('throws exception if model does not use HasImpersonation trait', function () {
    config([
        'impersonate.model' => User2::class,
    ]);

    $foo = User2::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
    ]);

    expect(fn () => impersonate()->begin($foo, $foo))
        ->toThrow(ImpersonateException::class, config('impersonate.model') . ' does not use ' . HasImpersonation::class);
});
