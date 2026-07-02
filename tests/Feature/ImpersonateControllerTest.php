<?php

use Octopy\Impersonate\Tests\Models\User1;

it('gets list of users to impersonate', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => true,
    ]);

    $bar = User1::create([
        'name'  => 'Bar Baz',
        'email' => 'bar@baz.qux',
        'admin' => false,
    ]);

    $baz = User1::create([
        'name'  => 'Baz Qux',
        'email' => 'baz@qux.foo',
        'admin' => true,
    ]);

    $qux = User1::create([
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

    expect($response->json('data'))->toHaveCount(2);
});

it('gets users with query', function () {
    User1::create([
        'name'  => 'Supian M',
        'email' => 'supianidz@github.com',
        'admin' => false,
    ]);

    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => true,
    ]);

    $bar = User1::create([
        'name'  => 'Bar Baz',
        'email' => 'bar@baz.qux',
        'admin' => false,
    ]);

    $baz = User1::create([
        'name'  => 'Baz Qux',
        'email' => 'baz@qux.foo',
        'admin' => true,
    ]);

    $qux = User1::create([
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

    expect($response->json('data'))->toHaveCount(1);
});

it('allows impersonator to take over impersonated user', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => true,
    ]);

    $bar = User1::create([
        'name'  => 'Bar Baz',
        'email' => 'bar@baz.com',
        'admin' => false,
    ]);

    $response = $this->actingAs($foo)->json('POST', route('impersonate.login'), [
        'user' => $bar->id,
    ]);

    $response->assertStatus(200);

    expect($this->impersonate->check())->toBeTrue();
});

it('allows impersonator to leave impersonation', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => true,
    ]);

    $bar = User1::create([
        'name'  => 'Bar Baz',
        'email' => 'bar@baz.com',
        'admin' => false,
    ]);

    $response = $this->actingAs($foo)->json('POST', route('impersonate.login'), [
        'user' => $bar->id,
    ]);

    $response->assertStatus(200);

    $this->actingAs($bar)->json('POST', route('impersonate.leave'));

    expect($this->impersonate->check())->toBeFalse();
});

it('prevents impersonator without permission from taking over', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => false, // cannot impersonate
    ]);

    $bar = User1::create([
        'name'  => 'Bar Baz',
        'email' => 'bar@baz.com',
        'admin' => false,
    ]);

    $response = $this->actingAs($foo)->json('POST', route('impersonate.login'), [
        'user' => $bar->id,
    ]);

    $response->assertStatus(500);
});

it('prevents taking over user who cannot be impersonated', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => true,
    ]);

    $bar = User1::create([
        'name'  => 'Bar Baz',
        'email' => 'bar@baz.com',
        'admin' => true, // cannot be impersonated
    ]);

    $response = $this->actingAs($foo)->json('POST', route('impersonate.login'), [
        'user' => $bar->id,
    ]);

    $response->assertStatus(500);
});
