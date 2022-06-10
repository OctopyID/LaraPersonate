<?php

namespace Octopy\Impersonate\Tests\Unit;

use Octopy\Impersonate\Impersonate;
use Octopy\Impersonate\ImpersonateRepository;
use Octopy\Impersonate\Tests\Models\User;
use Octopy\Impersonate\Tests\TestCase;

class ImpersonateRepositoryTest extends TestCase
{
    /**
     * @var ImpersonateRepository
     */
    protected ImpersonateRepository $repository;

    /**
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();

        $this->repository = new ImpersonateRepository($this->impersonate);
    }

    /**
     * @return void
     */
    public function testItGetImpersonatorInStorage() : void
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

        $this->actingAs($foo);

        $foo->impersonate($bar);

        $this->assertEquals($foo->refresh()->toArray(), $this->repository->getImpersonatorInStorage()->toArray());
    }

    /**
     * @return void
     */
    public function testItGetImpersonatedInStorage() : void
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

        $this->actingAs($foo);

        $foo->impersonate($bar);

        $this->assertEquals($bar->refresh()->toArray(), $this->repository->getImpersonatedInStorage()->toArray());
    }

    /**
     * @return void
     */
    public function testGetImpersonationUsers() : void
    {
        User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
            'admin' => true,
        ]);

        User::create([
            'name'  => 'Bar Baz',
            'email' => 'bar@baz.qux',
            'admin' => false,
        ]);

        User::create([
            'name'  => 'Baz Qux',
            'email' => 'baz@qux.foo',
            'admin' => true,
        ]);

        User::create([
            'name'  => 'Qux Foo',
            'email' => 'qux@foo.bar',
            'admin' => false,
        ]);

        $this->assertCount(2, $this->repository->getUsers());
    }
}
