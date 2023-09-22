<?php

namespace Octopy\Impersonate\Tests\Feature;

use Illuminate\Testing\Fluent\AssertableJson;
use Octopy\Impersonate\Tests\Models\User;
use Octopy\Impersonate\Tests\TestCase;

class ImpersonateControllerTest extends TestCase
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
                'data' => [
                    '*' => ['key', 'val'],
                ],
            ]);

        $response->assertSee($bar->name);
        $response->assertSee($qux->name);

        // non impersonated users should not be shown
        $response->assertDontSee($foo->name);
        $response->assertDontSee($baz->name);

        $this->assertCount(2, $response->json('data'));
    }

    /**
     * @return void
     */
    public function testGetUsersWithQuery() : void
    {
        User::create([
            'name'  => 'Supian M',
            'email' => 'supianidz@github.com',
            'admin' => false,
        ]);

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

        $response = $this->actingAs($foo)->json('GET', route('impersonate.index'), [
            'query' => 'supian',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['key', 'val'],
                ],
            ]);

        $this->assertCount(1, $response->json('data'));
    }

    /**
     * @return void
     */
    public function testImpersonatorCanTakeOverImpersonatedUser() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        $bar = User::create([
            'name'  => 'Bar Baz',
            'email' => 'bar@baz.com',
            'admin' => false,
        ]);

        $response = $this->actingAs($foo)->json('POST', route('impersonate.login'), [
            'user' => $bar->id,
        ]);

        $response->assertStatus(200);

        $this->assertTrue($this->impersonate->check());
    }

    /**
     * @return void
     */
    public function testImpersonatorCanLeaveImpersonation() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        $bar = User::create([
            'name'  => 'Bar Baz',
            'email' => 'bar@baz.com',
            'admin' => false,
        ]);

        $response = $this->actingAs($foo)->json('POST', route('impersonate.login'), [
            'user' => $bar->id,
        ]);

        $response->assertStatus(200);

        $this->actingAs($bar)->json('POST', route('impersonate.leave'));

        $this->assertFalse($this->impersonate->check());
    }
}
