<?php

namespace Octopy\Impersonate\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface Storage
{
    /**
     * @return bool
     */
    public function isInImpersonatingMode() : bool;

    /**
     * @param  Authenticatable $user
     * @return $this
     */
    public function setImpersonatorIdentifier(Authenticatable $user) : self;

    /**
     * @return mixed
     */
    public function getImpersonatorIdentifier() : mixed;

    /**
     * @param  Authenticatable $user
     * @return $this
     */
    public function setImpersonatedIdentifier(Authenticatable $user) : self;

    /**
     * @return mixed
     */
    public function getImpersonatedIdentifier() : mixed;
}
