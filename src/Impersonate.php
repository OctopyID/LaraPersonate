<?php

namespace Octopy\Impersonate;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Octopy\Impersonate\Contracts\Impersonation;
use Octopy\Impersonate\Events\LeaveImpersonation;
use Octopy\Impersonate\Events\BeginImpersonation;
use Octopy\Impersonate\Exceptions\ImpersonateException;
use Octopy\Impersonate\Storage\SessionStorage;
use Illuminate\Contracts\Auth\StatefulGuard;

class Impersonate
{
    /**
     * @var StatefulGuard
     */
    protected StatefulGuard $guard;

    /**
     * @var Repository
     */
    protected Repository $repository;

    /**
     * @var SessionStorage
     */
    protected SessionStorage $storage;

    /**
     * Impersonate constructor.
     */
    public function __construct()
    {
        $this->repository = new Repository;
        $this->storage = new SessionStorage;

        $this->guard(config(
            'impersonate.guard'
        ));
    }

    /**
     * Set auth guard.
     *
     * @param  string $guard
     * @return $this
     */
    public function guard(string $guard) : self
    {
        $this->guard = Auth::guard($guard);

        return $this;
    }

    /**
     * @return bool
     */
    public function authorized() : bool
    {
        return $this->guard->check();
    }

    /**
     * @return bool
     */
    public function check() : bool
    {
        return $this->storage->isInImpersonatingMode();
    }

    /**
     * @return Model|Authenticatable|Impersonation
     */
    public function impersonator() : Model|Authenticatable|Impersonation
    {
        if ($this->check()) {
            return $this->repository->find($this->storage->getImpersonator());
        }

        return $this->guard->user();
    }

    /**
     * @return Model|Authenticatable|Impersonation
     */
    public function impersonated() : Model|Authenticatable|Impersonation
    {
        return $this->repository->find($this->storage->getImpersonated());
    }

    /**
     * @param  mixed $impersonator
     * @param  mixed $impersonated
     * @return $this
     * @throws ImpersonateException
     */
    public function begin(mixed $impersonator, mixed $impersonated) : Impersonate
    {
        if (! $impersonator instanceof Model) {
            $impersonator = $this->repository->find($impersonator);
        }

        if (! $impersonated instanceof Model) {
            $impersonated = $this->repository->find($impersonated);
        }

        if ($this->validate($impersonator, $impersonated)) {
            $this->storage
                ->setImpersonator($impersonator)
                ->setImpersonated($impersonated);

            $this->guard->login($impersonated);

            event(new BeginImpersonation(
                $impersonator, $impersonated
            ));
        }

        return $this;
    }

    /**
     * @return Impersonate
     */
    public function leave() : Impersonate
    {
        if ($this->check()) {
            $impersonator = $this->impersonator();
            $impersonated = $this->impersonated();

            if ($this->storage->flush()) {
                $this->guard->login($impersonator);

                event(new LeaveImpersonation(
                    $impersonator, $impersonated
                ));
            }
        }

        return $this;
    }

    /**
     * @param  Model $impersonator
     * @param  Model $impersonated
     * @return bool
     * @throws ImpersonateException
     */
    private function validate(Model $impersonator, Model $impersonated) : bool
    {
        if ($impersonator->is($impersonated)) {
            throw new ImpersonateException('You cannot impersonate yourself.');
        }

        /**
         * @var $authorization Authorization
         */
        $authorization = App::make('impersonate.authorization');
        if (! $authorization->check('impersonator', $impersonator)) {
            throw new ImpersonateException('You don\'t have the ability to impersonate.');
        }

        if (! $authorization->check('impersonated', $impersonated)) {
            throw new ImpersonateException('You can\'t impersonate this user.');
        }

        return true;
    }
}
