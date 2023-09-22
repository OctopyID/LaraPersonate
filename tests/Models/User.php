<?php

namespace Octopy\Impersonate\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Octopy\Impersonate\Authorization;
use Octopy\Impersonate\Concerns\HasImpersonation;
use Octopy\Impersonate\Contracts\Impersonation;

/**
 * @method   static create(string[] $array)
 * @property string $name
 */
class User extends \Illuminate\Foundation\Auth\User
{
    use HasImpersonation;

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
            'name',
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
}
