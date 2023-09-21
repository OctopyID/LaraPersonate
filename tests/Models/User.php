<?php

namespace Octopy\Impersonate\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Octopy\Impersonate\Authorization;
use Octopy\Impersonate\Concerns\HasImpersonation;
use Octopy\Impersonate\Contracts\Impersonation;

/**
 * @method static create(string[] $array)
 */
class User extends \Illuminate\Foundation\Auth\User
{
    use HasImpersonation;

    protected $fillable = [
        'name', 'email', 'admin'
    ];

    /**
     * @param  Authorization $authorization
     * @return void
     */
    public function impersonatable(Authorization $authorization) : void
    {
        $authorization
            ->impersonator(function ($user) {
                return $user->admin;
            })
            ->impersonated(function ($user) {
                return ! $user->admin;
            });
    }
}
