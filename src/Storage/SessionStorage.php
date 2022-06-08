<?php

namespace Octopy\Impersonate\Storage;

use Illuminate\Contracts\Auth\Authenticatable;
use Octopy\Impersonate\Contracts\Storage;

class SessionStorage implements Storage
{
    /**
     * @return bool
     */
    public function isInImpersonatingMode() : bool
    {
        return session()->has('impersonate');
    }

    /**
     * @param  Authenticatable $user
     * @return Storage
     */
    public function setImpersonatorIdentifier(Authenticatable $user) : Storage
    {
        session([
            'impersonate.impersonator' => encrypt($user->getAuthIdentifier()),
        ]);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImpersonatorIdentifier() : mixed
    {
        return decrypt(session('impersonate.impersonator'));
    }

    /**
     * @param  Authenticatable $user
     * @return Storage
     */
    public function setImpersonatedIdentifier(Authenticatable $user) : Storage
    {
        session([
            'impersonate.impersonated' => encrypt($user->getAuthIdentifier()),
        ]);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImpersonatedIdentifier() : mixed
    {
        return decrypt(session('impersonate.impersonated'));
    }

    /**
     * @return bool
     */
    public function clearStorage() : bool
    {
        session()->forget([
            'impersonate',
            'impersonate.impersonator',
            'impersonate.impersonated',
        ]);

        return true;
    }
}
