<?php

namespace Octopy\Impersonate\Tests\Unit\Storage;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Octopy\Impersonate\Contracts\Storage;
use Octopy\Impersonate\Storage\SessionStorage;
use Octopy\Impersonate\Tests\Models\User;
use Octopy\Impersonate\Tests\TestCase;

class SessionStorageTest extends TestCase
{
    /**
     * @var Storage
     */
    protected Storage $storage;

    /**
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();

        $this->storage = new SessionStorage;
    }

    /**
     * @return void
     */
    public function testItCanSetImpersonatorIdentifier() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
        ]);

        $bar = User::create([
            'name'  => 'Bar Baz',
            'email' => 'bar@baz.qux',
        ]);

        $this->storage->setImpersonatorIdentifier($foo);
        $this->storage->setImpersonatedIdentifier($bar);

        $this->assertTrue(session()->has('impersonate.impersonator'));
        $this->assertTrue(session()->has('impersonate.impersonated'));
    }

    /**
     * @return void
     */
    public function testItCanGetImpersonatorIdentifier() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
        ]);

        $bar = User::create([
            'name'  => 'Bar Baz',
            'email' => 'bar@baz.qux',
        ]);

        $this->storage->setImpersonatorIdentifier($foo);
        $this->storage->setImpersonatedIdentifier($bar);

        $this->assertEquals($foo->id, $this->storage->getImpersonatorIdentifier());
        $this->assertEquals($bar->id, $this->storage->getImpersonatedIdentifier());
    }

    /**
     * @return void
     */
    public function testItCanClearSession() : void
    {
        $foo = User::create([
            'name'  => 'Foo Bar',
            'email' => 'foo@bar.baz',
        ]);

        $bar = User::create([
            'name'  => 'Bar Baz',
            'email' => 'bar@baz.qux',
        ]);

        $this->storage->setImpersonatorIdentifier($foo);
        $this->storage->setImpersonatedIdentifier($bar);

        $this->assertTrue($this->storage->isInImpersonatingMode());

        $this->storage->clearStorage();

        $this->assertFalse($this->storage->isInImpersonatingMode());
    }
}
