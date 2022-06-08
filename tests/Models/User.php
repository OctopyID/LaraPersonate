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

    /**
     * @return bool
     */
    public function canImpersonate() : bool
    {
        return $this->admin;
    }

    /**
     * @return bool
     */
    public function canBeImpersonated() : bool
    {
        return ! $this->admin;
    }
}
