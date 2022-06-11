<?php

namespace Octopy\Impersonate\Concerns;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;
use Octopy\Impersonate\Exceptions\ImpersonateException;
use Octopy\Impersonate\Impersonate as Manager;
use Octopy\Impersonate\Impersonation;

trait Impersonate
{
    /**
     * @return void
     */
    public static function bootImpersonate() : void
    {
        (new static)->impersonatable(App::make(
            'impersonation'
        ));
    }

    /**
     * @return Manager
     * @throws ImpersonateException
     */
    public function getImpersonateAttribute() : Manager
    {
        return $this->impersonate();
    }

    /**
     * @param  Impersonation $impersonation
     * @return void
     */
    public abstract function impersonatable(Impersonation $impersonation) : void;

    /**
     * @param  User|null $user
     * @return User|Manager
     * @throws ImpersonateException
     */
    public function impersonate(User $user = null) : User|Manager
    {
        /**
         * @var Manager $manager
         */
        $manager = App::make('impersonate');

        if ($user) {
            return $manager->impersonate($this, $user);
        }

        return $manager;
    }
}
