<?php

namespace Octopy\LaraPersonate;

use JetBrains\PhpStorm\Pure;
use Illuminate\Support\Facades\Auth;
use Octopy\LaraPersonate\Storage\Session;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class Impersonate
 * @package Octopy\LaraPersonate
 */
class Impersonate
{
    /**
     * @var Session
     */
    protected Session $session;

    /**
     * Impersonate constructor.
     */
    #[Pure]
    public function __construct()
    {
        $this->session = new Session;
    }

    /**
     * @return bool
     */
    public function enabled() : bool
    {
        return config('impersonate.enabled', true);
    }

    /**
     * @param  int $prevUser
     * @param  int $nextUser
     * @return Authenticatable
     */
    public function take(int $prevUser, int $nextUser) : Authenticatable
    {
        return Auth::loginUsingId(
            $this->session->saveUserId($prevUser, $nextUser)
        );
    }

    /**
     * @return void
     */
    public function leave() : void
    {
        if ($this->login($this->session->getPrevUserId())) {
            $this->session->destroy();
        }
    }

    /**
     * @param  int $id
     * @return Authenticatable
     */
    protected function login(int $id) : Authenticatable
    {
        return Auth::loginUsingId($id);
    }
}
