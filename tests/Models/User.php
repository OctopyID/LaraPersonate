<?php

namespace Octopy\Impersonate\Tests\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Octopy\Impersonate\Concerns\Impersonate;

/**
 * @method static create(string[] $array)
 * @property bool $admin
 */
class User extends Authenticatable
{
    use Impersonate;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'email', 'admin',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'admin' => 'boolean',
    ];
}
