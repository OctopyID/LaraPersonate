<?php

namespace Octopy\Impersonate;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Octopy\Impersonate\Concerns\HasImpersonation;
use Octopy\Impersonate\Events\BeginImpersonation;
use Octopy\Impersonate\Events\LeaveImpersonation;
use Octopy\Impersonate\Exceptions\ImpersonateException;
use Octopy\Impersonate\Storage\SessionStorage;

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
    public function check() : bool
    {
        return $this->storage->isInImpersonatingMode();
    }

    /**
     * @return bool
     */
    public function authorized() : bool
    {
        if (! in_array(HasImpersonation::class, class_uses(config('impersonate.model')))) {
            return false;
        }

        return $this->guard->check() && app('impersonate.authorization')->isImpersonator($this->impersonator());
    }

    /**
     * @return Model|Authenticatable
     */
    public function impersonator() : Model|Authenticatable
    {
        if ($this->check()) {
            return $this->repository->find($this->storage->getImpersonator());
        }

        return $this->guard->user();
    }

    /**
     * @return Model|Authenticatable
     */
    public function impersonated() : Model|Authenticatable
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
        $impersonator = $this->fetchModel($impersonator);
        $impersonated = $this->fetchModel($impersonated);

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
        if (! in_array(HasImpersonation::class, class_uses($impersonator))) {
            throw new ImpersonateException(get_class($impersonator) . ' does not uses ' . HasImpersonation::class);
        }

        if ($impersonator->is($impersonated)) {
            throw new ImpersonateException('You cannot impersonate yourself.');
        }

        /**
         * @var $authorization Authorization
         */
        $authorization = App::make('impersonate.authorization');
        if (! $authorization->isImpersonator($impersonator)) {
            throw new ImpersonateException('You don\'t have the ability to impersonate.');
        }

        if (! $authorization->isImpersonated($impersonated)) {
            throw new ImpersonateException('You can\'t impersonate this user.');
        }

        return true;
    }

    /**
     * @param  mixed $modelOrId
     * @return Model|Authenticatable
     */
    private function fetchModel(mixed $modelOrId) : Model|Authenticatable
    {
        if (! $modelOrId instanceof Model) {
            return $this->repository->find($modelOrId);
        }

        return $modelOrId;
    }
}
