<?php

use Octopy\Impersonate\Repository;
use Octopy\Impersonate\Tests\Models\User1;

beforeEach(function () {
    config([
        'impersonate.model' => User1::class,
    ]);

    $this->repository = new Repository;
});

it('can search for users basically', function () {
    User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => false,
    ]);

    User1::create([
        'name'  => 'Bar Foo',
        'email' => 'bar@foo.baz',
        'admin' => false,
    ]);

    expect($this->repository->get('Qux')->items())->toHaveCount(0)
        ->and($this->repository->get('Foo')->items())->toHaveCount(2)
        ->and($this->repository->get('Bar')->items())->toHaveCount(2)
        ->and($this->repository->get('Foo Bar')->items())->toHaveCount(1)
        ->and($this->repository->get('Bar Foo')->items())->toHaveCount(1);
});

it('can search by relations', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => false,
    ]);

    $foo->posts()->create([
        'title' => 'ABC',
    ]);

    $foo->posts()->create([
        'title' => 'DEF',
    ]);

    expect($this->repository->get('LOL')->items())->toHaveCount(0)
        ->and($this->repository->get('ABC')->items())->toHaveCount(1)
        ->and($this->repository->get('DEF')->items())->toHaveCount(1);
});

it('handles trashed users based on config', function () {
    $foo = User1::create([
        'name'  => 'Foo Bar',
        'email' => 'foo@bar.baz',
        'admin' => false,
    ]);

    $bar = User1::create([
        'name'  => 'Bar Foo',
        'email' => 'bar@foo.baz',
        'admin' => false,
    ]);

    expect($this->repository->get('Foo')->items())->toHaveCount(2);

    config([
        'impersonate.trashed' => 0,
    ]);
    $bar->delete();

    expect($this->repository->get('Foo')->items())->toHaveCount(1);

    config([
        'impersonate.trashed' => 1,
    ]);

    expect($this->repository->get('Foo')->items())->toHaveCount(2);
});
