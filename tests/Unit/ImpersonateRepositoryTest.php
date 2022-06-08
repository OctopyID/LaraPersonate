<?php

namespace Octopy\Impersonate\Tests\Unit;

use Octopy\Impersonate\Impersonate;
use Octopy\Impersonate\ImpersonateRepository;
use Octopy\Impersonate\Tests\Models\User;
use Octopy\Impersonate\Tests\TestCase;

class ImpersonateRepositoryTest extends TestCase
{
    /**
     * @var Impersonate
     */
    protected Impersonate $impersonate;

    /**
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();

        $this->impersonate = $this->app->make('impersonate');
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

        $repository = new ImpersonateRepository($this->impersonate);

        $this->assertEquals($foo->refresh()->toArray(), $repository->getImpersonatorInStorage()->toArray());
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

        $repository = new ImpersonateRepository($this->impersonate);

        $this->assertEquals($bar->refresh()->toArray(), $repository->getImpersonatedInStorage()->toArray());
    }
}
