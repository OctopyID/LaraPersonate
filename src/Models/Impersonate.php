<?php

namespace Octopy\LaraPersonate\Models;

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
}
