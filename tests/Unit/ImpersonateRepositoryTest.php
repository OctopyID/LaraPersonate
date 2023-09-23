<?php

namespace Octopy\Impersonate\Tests\Unit;

use Octopy\Impersonate\Repository;
use Octopy\Impersonate\Tests\Models\User1;
use Octopy\Impersonate\Tests\TestCase;

class ImpersonateRepositoryTest extends TestCase
{
    /**
     * @var Repository
     */
    protected Repository $repository;

    /**
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();

        config([
            'impersonate.model' => User1::class,
        ]);

        $this->repository = new Repository;
    }

    /**
     * @return void
     */
    public function testBasicSearch() : void
    {
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

        $this->assertCount(0, $this->repository->get('Qux')->items());
        $this->assertCount(2, $this->repository->get('Foo')->items());
        $this->assertCount(2, $this->repository->get('Bar')->items());
        $this->assertCount(1, $this->repository->get('Foo Bar')->items());
        $this->assertCount(1, $this->repository->get('Bar Foo')->items());
    }

    /**
     * @return void
     */
    public function testSearchByRelations() : void
    {
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

        $this->assertCount(0, $this->repository->get('LOL')->items());
        $this->assertCount(1, $this->repository->get('ABC')->items());
        $this->assertCount(1, $this->repository->get('DEF')->items());
    }

    /**
     * @return void
     */
    public function testUserTrashedHandling() : void
    {
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

        $this->assertCount(2, $this->repository->get('Foo')->items());

        config([
            'impersonate.trashed' => 0,
        ]);
        $bar->delete();

        $this->assertCount(1, $this->repository->get('Foo')->items());

        config([
            'impersonate.trashed' => 1,
        ]);

        $this->assertCount(2, $this->repository->get('Foo')->items());
    }
}
