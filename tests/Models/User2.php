<?php

namespace Octopy\Impersonate\Tests\Models;

/**
 * @method   static create(string[] $array)
 * @property string $name
 */
class User2 extends \Illuminate\Foundation\Auth\User
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
