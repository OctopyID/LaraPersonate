<?php

use Illuminate\Support\Facades\Route;
use Octopy\Impersonate\Http\Middleware\ImpersonateMiddleware;
use Octopy\Impersonate\Tests\Models\User1;
use Symfony\Component\HttpFoundation\StreamedResponse;

beforeEach(function () {
    Route::get('foo', fn () => '<html lang="en"><body>Hello World</body></html>')->name('foo')->middleware(ImpersonateMiddleware::class);
});

it('appears on users allowed to impersonate', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => true,
    ]);

    $this->actingAs($foo)->get('foo')->assertSee('impersonate');
});

it('does not appear on users not allowed to impersonate', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => false,
    ]);

    $this->actingAs($foo)->get('foo')->assertDontSee('impersonate');
});

it('does not appear when disabled', function () {
    config([
        'impersonate.interface.enabled' => false,
    ]);

    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => true,
    ]);

    $this->actingAs($foo)->get('foo')->assertDontSee('impersonate');
});

it('does not appear on excluded urls', function () {
    config([
        'impersonate.except' => [
            'foo',
        ],
    ]);

    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => true,
    ]);

    $this->actingAs($foo)->get('foo')->assertDontSee('impersonate');
});

it('does not appear when request is ajax or wants json', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => true,
    ]);

    $this->actingAs($foo)
        ->get('foo', [
            'Accept' => 'application/json',
        ])
        ->assertDontSee('impersonate');
});

it('does not appear on json response', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => true,
    ]);

    Route::get('bar', function () {
        return ['foo' => 'bar'];
    })->middleware(ImpersonateMiddleware::class);

    $this->actingAs($foo)->get('bar')->assertDontSee('impersonate');
});

it('does not appear on binary file response', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => true,
    ]);

    Route::get('bar', function () {
        return response()->download(__DIR__ . '/../../LICENSE');
    })->middleware(ImpersonateMiddleware::class);

    $this->actingAs($foo)->get('bar')->assertDontSee('impersonate');
});

it('does not appear on redirect response', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => true,
    ]);

    Route::get('bar', function () {
        return response()->redirectToRoute('foo');
    })->middleware(ImpersonateMiddleware::class);

    $this->actingAs($foo)->get('bar')->assertDontSee('impersonate');
});

it('does not appear on stream response', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => true,
    ]);

    Route::get('bar', function () {
        return new StreamedResponse(fn () => 'Hello World');
    })->middleware(ImpersonateMiddleware::class);

    $this->actingAs($foo)->get('bar')->assertDontSee('impersonate');
});

it('does not appear on exception response', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => true,
    ]);

    Route::get('bar', function () {
        throw new Exception('Hello World');
    })->middleware(ImpersonateMiddleware::class);

    $this->actingAs($foo)->get('bar')->assertDontSee('impersonate');
});

it('appears on html without body tag', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => true,
    ]);

    Route::get('no-body', fn () => '<html><h1>Hello World</h1></html>')->middleware(ImpersonateMiddleware::class);

    $this->actingAs($foo)->get('no-body')->assertSee('impersonate');
});

it('does not throw exception for unauthenticated users', function () {
    Route::get('guest', fn () => '<html><body>Hello World</body></html>')->middleware(ImpersonateMiddleware::class);

    $this->get('guest')->assertDontSee('impersonate')->assertStatus(200);
});
