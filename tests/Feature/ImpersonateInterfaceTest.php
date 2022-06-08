<?php

namespace Octopy\Impersonate\Tests\Feature;

use Octopy\Impersonate\Tests\Models\User;
use Octopy\Impersonate\Tests\TestCase;

class ImpersonateInterfaceTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetUsers() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        $bar = User::create([
            'name'  => 'Bar Baz',
            'email' => 'bar@baz.qux',
            'admin' => false,
        ]);

        $baz = User::create([
            'name'  => 'Baz Qux',
            'email' => 'baz@qux.foo',
            'admin' => true,
        ]);

        $qux = User::create([
            'name'  => 'Qux Foo',
            'email' => 'qux@foo.bar',
            'admin' => false,
        ]);

        $response = $this->actingAs($foo)->json('GET', route('impersonate.index'));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => config('impersonate.field.columns'),
            ]);

        $response->assertDontSee($foo->name);
    }
}
