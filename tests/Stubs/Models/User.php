<?php

namespace Octopy\LaraPersonate\Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Octopy\LaraPersonate\Models\Impersonate;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @property bool $admin
 * @property bool $impersonated
 * @package Octopy\LaraPersonate\Tests\Stubs\Models
 * @method static create(array $array)
 * @method static superAdmin()
 * @method static user()
 * @method static admin()
 */
class User extends Authenticatable
{
    use Impersonate;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'admin', 'impersonated',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return bool
     */
    public function canImpersonate() : bool
    {
        return $this->attributes['admin'] == 1;
    }

    /**
     * @return bool
     */
    public function canBeImpersonated() : bool
    {
        return $this->attributes['impersonated'] == 1;
    }

    /**
     * @param  Builder $query
     * @return Model
     */
    public function scopeUser(Builder $query) : Model
    {
        return $query->where('email', 'user@example.com')->first();
    }

    /**
     * @param  Builder $query
     * @return Model
     */
    public function scopeAdmin(Builder $query) : Model
    {
        return $query->where('email', 'admin@example.com')->first();
    }

    /**
     * @param  Builder $query
     * @return Model
     */
    public function scopeSuperAdmin(Builder $query) : Model
    {
        return $query->where('email', 'super@example.com')->first();
    }
}
