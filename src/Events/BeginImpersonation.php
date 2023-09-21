<?php

namespace Octopy\Impersonate\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;

class BeginImpersonation
{
    use Dispatchable;

    /**
     * @param  Authenticatable $impersonator
     * @param  Authenticatable $impersonated
     */
    public function __construct(public Authenticatable $impersonator, public Authenticatable $impersonated)
    {
        //
    }
}
