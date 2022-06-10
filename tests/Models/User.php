<?php

namespace Octopy\Impersonate\Tests\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    /**
     * @return HasOne
     */
    public function comment() : HasOne
    {
        return $this->hasOne(Comment::class);
    }

    /**
     * @return HasMany
     */
    public function comments() : HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
