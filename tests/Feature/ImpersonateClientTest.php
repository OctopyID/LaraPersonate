<?php

namespace Octopy\Impersonate\Tests\Feature;

use Exception;
use Illuminate\Support\Facades\Route;
use Octopy\Impersonate\Http\Middleware\ImpersonateMiddleware;
use Octopy\Impersonate\Tests\Models\User;
use Octopy\Impersonate\Tests\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImpersonateClientTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();

        Route::get('foo', fn() => '<html lang="en"><body>Hello World</body></html>')->name('foo')->middleware(ImpersonateMiddleware::class);
    }

    /**
     * @return void
     */
    public function testInterfaceAppearsOnUsersAllowedToImpersonate() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        $this->actingAs($foo)->get('foo')->assertSee('impersonate');
    }

    /**
     * @return void
     */
    public function testInterfaceDoesNotAppearOnUsersNotAllowedToImpersonate() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => false,
        ]);

        $this->actingAs($foo)->get('foo')->assertDontSee('impersonate');
    }

    /**
     * @return void
     */
    public function testInterfaceDoesNotAppearWhenDisabled() : void
    {
        config([
            'impersonate.enabled' => false,
        ]);

        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        $this->actingAs($foo)->get('foo')->assertDontSee('impersonate');
    }

    /**
     * @return void
     */
    public function testImpersonateDoesNotAppearOnExcludedUrls() : void
    {
        config([
            'impersonate.except' => [
                'foo',
            ],
        ]);

        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        $this->actingAs($foo)->get('foo')->assertDontSee('impersonate');
    }

    /**
     * @return void
     */
    public function testImpersonateDoesNotAppearWhenRequestIsAjaxOrWantJson() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        $this
            ->actingAs($foo)
            ->get('foo', [
                'Accept' => 'application/json',
            ])
            ->assertDontSee('impersonate');
    }

    /**
     * @return void
     */
    public function testImpersonateDoesNotAppearOnJsonResponse() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        Route::get('bar', function () {
            return ['foo' => 'bar'];
        })
            ->middleware(ImpersonateMiddleware::class);

        $this->actingAs($foo)->get('bar')->assertDontSee('impersonate');
    }

    /**
     * @return void
     */
    public function testImpersonateDoesNotAppearOnBinaryFileResponse() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        Route::get('bar', function () {
            return response()->download(__DIR__ . '/../../LICENSE');
        })
            ->middleware(ImpersonateMiddleware::class);

        $this->actingAs($foo)->get('bar')->assertDontSee('impersonate');
    }

    /**
     * @return void
     */
    public function testImpersonateDoesNotAppearOnRedirectResponse() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        Route::get('bar', function () {
            return response()->redirectToRoute('foo');
        })
            ->middleware(ImpersonateMiddleware::class);

        $this->actingAs($foo)->get('bar')->assertDontSee('impersonate');
    }

    /**
     * @return void
     */
    public function testImpersonateDoesNotAppearOnStreamResponse() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        Route::get('bar', function () {
            return new StreamedResponse(fn() => 'Hello World');
        })
            ->middleware(ImpersonateMiddleware::class);

        $this->actingAs($foo)->get('bar')->assertDontSee('impersonate');
    }

    /**
     * @return void
     */
    public function testImpersonateDoesNotAppearOnExceptionResponse() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        Route::get('bar', function () {
            throw new Exception('Hello World');
        })
            ->middleware(ImpersonateMiddleware::class);

        $this->actingAs($foo)->get('bar')->assertDontSee('impersonate');
    }
}
