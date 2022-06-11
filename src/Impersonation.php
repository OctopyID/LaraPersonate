<?php

namespace Octopy\Impersonate;

use Closure;
use Illuminate\Foundation\Auth\User;

final class Impersonation
{
    /**
     * @var Closure
     */
    protected Closure $impersonator;

    /**
     * @var Closure
     */
    protected Closure $impersonated;

    /**
     * @param  Impersonate $impersonate
     */
    public function __construct(protected Impersonate $impersonate)
    {
        //
    }

    /**
     * @param  string $name
     * @param  User   $user
     * @return bool
     */
    public function check(string $name, User $user) : bool
    {
        return call_user_func($this->$name, $user);
    }

    /**
     * @param  Closure $param
     * @return Impersonation
     */
    public function impersonator(Closure $param) : Impersonation
    {
        $this->impersonator = $param;

        return $this;
    }

    /**
     * @param  Closure $param
     * @return Impersonation
     */
    public function impersonated(Closure $param) : Impersonation
    {
        $this->impersonated = $param;

        return $this;
    }
}
