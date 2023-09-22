<?php

namespace Octopy\Impersonate\Storage;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class SessionStorage
{
    /**
     * @return bool
     */
    public function isInImpersonatingMode() : bool
    {
        return session()->has('impersonate');
    }

    /**
     * @param  Model $model
     * @return $this
     */
    public function setImpersonator(Model $model) : self
    {
        session([
            'impersonate.impersonator' => $model->getKey(),
        ]);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImpersonator() : mixed
    {
        return session('impersonate.impersonator');
    }

    /**
     * @param  Model $model
     * @return $this
     */
    public function setImpersonated(Model $model) : self
    {
        session([
            'impersonate.impersonated' => $model->getKey(),
        ]);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImpersonated() : mixed
    {
        return session('impersonate.impersonated');
    }

    /**
     * @return bool
     */
    public function flush() : bool
    {
        session()->forget([
            'impersonate',
            'impersonate.impersonator',
            'impersonate.impersonated',
        ]);

        return true;
    }
}
