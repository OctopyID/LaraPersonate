<?php

namespace Octopy\Impersonate\Concerns;

use Illuminate\Support\Facades\App;
use Octopy\Impersonate\Exceptions\ImpersonateException;
use Octopy\Impersonate\Impersonate;

trait HasImpersonation
{
    /**
     * Determine if the user can impersonate other users.
     *
     * @return bool
     */
    public function canImpersonate() : bool
    {
        return false;
    }

    /**
     * Determine if the user can be impersonated by other users.
     *
     * @return bool
     */
    public function canBeImpersonated() : bool
    {
        return false;
    }

    /**
     * @param  mixed $impersonated
     * @return Impersonate
     * @throws ImpersonateException
     */
    public function impersonate(mixed $impersonated = null) : Impersonate
    {
        $manager = App::make('impersonate');

        if ($impersonated) {
            $manager->begin($this, $impersonated);
        }

        return $manager;
    }
}
