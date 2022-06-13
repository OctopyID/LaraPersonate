<?php

namespace Octopy\Impersonate\Tests\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Octopy\Impersonate\Concerns\Impersonate;
use Octopy\Impersonate\ImpersonateAuthorization;

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
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::created(function (User $user) {
            $user->comments()->create([
                'comment' => Str::random(),
            ]);
        });
    }

    /**
     * @param  ImpersonateAuthorization $authorization
     * @return void
     */
    public function impersonatable(ImpersonateAuthorization $authorization) : void
    {
        $authorization->impersonator(function (User $user) {
            return $user->admin;
        });

        $authorization->impersonated(function (User $user) {
            return ! $user->admin;
        });
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
