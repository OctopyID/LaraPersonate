<?php

namespace Octopy\LaraPersonate\AuthorizationDrivers;

/**
 * Class DefaultDriver
 *
 * @package Octopy\LaraPersonate\AuthorizationDrivers
 */
class DefaultAuthorizationDriver extends AuthorizationDriver
{
    /**
     * @return mixed|void
     */
    public function handle()
    {
        return $this->reMap(
            $this->model->limit(config('impersonate.limit', 3))->get()
        );
    }
}
