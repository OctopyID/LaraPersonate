<?php

namespace Octopy\Impersonate\Concerns;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;
use Octopy\Impersonate\Exceptions\ImpersonateException;
use Octopy\Impersonate\ImpersonateManager;
use Octopy\Impersonate\ImpersonateAuthorization;

trait Impersonate
{
    /**
     * @return void
     */
    public static function bootImpersonate() : void
    {
        (new static)->impersonatable(App::make(
            'impersonate.authorization'
        ));
    }

    /**
     * @return ImpersonateManager
     * @throws ImpersonateException
     */
    public function getImpersonateAttribute() : ImpersonateManager
    {
        return $this->impersonate();
    }

    /**
     * @param  ImpersonateAuthorization $impersonation
     * @return void
     */
    public abstract function impersonatable(ImpersonateAuthorization $impersonation) : void;

    /**
     * @param  User|int|string|null $user
     * @return User|ImpersonateManager
     * @throws ImpersonateException
     */
    public function impersonate(User|int|string $user = null) : User|ImpersonateManager
    {
        /**
         * @var ImpersonateManager $manager
         */
        $manager = App::make('impersonate');

        if ($user) {
            return $manager->impersonate($this, $user);
        }

        return $manager;
    }
}
