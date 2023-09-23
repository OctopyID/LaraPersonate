<?php

namespace Octopy\Impersonate;

use Closure;

final class Authorization
{
    /**
     * @var Closure
     */
    private Closure $impersonator;

    /**
     * @var Closure
     */
    private Closure $impersonated;

    /**
     * Authorization constructor.
     */
    public function __construct()
    {
        $this->impersonator(function () {
            return true;
        });

        $this->impersonated(function () {
            return true;
        });
    }

    /**
     * @param  Closure $param
     * @return Authorization
     */
    public function impersonator(Closure $param) : Authorization
    {
        $this->impersonator = $param;

        return $this;
    }

    /**
     * @param  Closure $param
     * @return Authorization
     */
    public function impersonated(Closure $param) : Authorization
    {
        $this->impersonated = $param;

        return $this;
    }

    /**
     * @param  mixed $user
     * @return bool
     */
    public function isImpersonator(mixed $user) : bool
    {
        return call_user_func($this->impersonator, $user);
    }

    /**
     * @param  mixed $user
     * @return bool
     */
    public function isImpersonated(mixed $user) : bool
    {
        return call_user_func($this->impersonated, $user);
    }
}
