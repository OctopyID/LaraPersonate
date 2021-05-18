<?php

namespace Octopy\LaraPersonate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Octopy\LaraPersonate\Impersonate as ImpersonateManager;

/**
 * Trait Impersonate
 * @package Octopy\LaraPersonate\Models
 */
trait Impersonate
{
    /**
     * @return bool
     */
    public function canImpersonate() : bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canBeImpersonated() : bool
    {
        return true;
    }

    /**
     * @param  Model|string|int $user
     * @return mixed
     */
    public function impersonate($user)
    {
        return App::make(ImpersonateManager::class)->take($this, $user);
    }

    /**
     * @return mixed
     */
    public function leave()
    {
        return App::make(ImpersonateManager::class)->leave();
    }
}
