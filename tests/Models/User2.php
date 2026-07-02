<?php

namespace Octopy\Impersonate\Tests\Models;

use Illuminate\Foundation\Auth\User;

/**
 * @method   static create(string[] $array)
 * @property string $name
 */
class User2 extends User
{
    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'email', 'admin',
    ];
}
