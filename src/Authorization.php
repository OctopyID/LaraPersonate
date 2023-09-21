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
     * @param  string $name
     * @param  mixed  $user
     * @return bool
     */
    public function check(string $name, mixed $user) : bool
    {
        return call_user_func($this->$name, $user);
    }
}
