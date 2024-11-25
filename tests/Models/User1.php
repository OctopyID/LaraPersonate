<?php

namespace Octopy\Impersonate\Tests\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Octopy\Impersonate\Authorization;
use Octopy\Impersonate\Concerns\HasImpersonation;
use Octopy\Impersonate\Contracts\HasImpersonationUI;

/**
 * @method   static create(string[] $array)
 * @property string $name
 */
class User1 extends User implements HasImpersonationUI
{
    use HasImpersonation, SoftDeletes;

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

    /**
     * @return string[]
     */
    public function getImpersonateSearchField() : array
    {
        return [
            'name', 'posts.title',
        ];
    }

    /**
     * @return string
     */
    public function getImpersonateDisplayText() : string
    {
        return $this->name;
    }

    /**
     * @param  Authorization $authorization
     * @return void
     */
    public function setImpersonateAuthorization(Authorization $authorization) : void
    {
        $authorization
            ->impersonator(function ($user) {
                return $user->admin;
            })
            ->impersonated(function ($user) {
                return ! $user->admin;
            });
    }

    /**
     * @return HasMany
     */
    public function posts() : HasMany
    {
        return $this->hasMany(Post1::class, 'user_id');
    }
}
