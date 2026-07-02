<?php

namespace Octopy\Impersonate;

use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Laravel\Jetstream\Jetstream;
use Octopy\Impersonate\Concerns\HasImpersonation;
use Octopy\Impersonate\Events\BeginImpersonation;
use Octopy\Impersonate\Events\LeaveImpersonation;
use Octopy\Impersonate\Exceptions\ImpersonateException;
use Octopy\Impersonate\Storage\SessionStorage;

class Impersonate
{
    /**
     * @var Repository
     */
    protected Repository $repository;

    /**
     * @var SessionStorage
     */
    protected SessionStorage $session;

    /**
     * Impersonate constructor.
     */
    public function __construct(protected Auth $auth)
    {
        $this->repository = new Repository;
        $this->session = new SessionStorage;

        $guard = config('impersonate.guard');
        if (is_string($guard)) {
            $this->guard($guard);
        }
    }

    /**
     * Set auth guard.
     *
     * @param  string $guard
     * @return $this
     */
    public function guard(string $guard) : self
    {
        $this->auth->guard($guard);

        return $this;
    }

    /**
     * @return bool
     */
    public function check() : bool
    {
        return $this->session->isInImpersonatingMode();
    }

    /**
     * @return bool
     */
    public function authorized() : bool
    {
        $modelClass = config('impersonate.model');
        if (! is_string($modelClass) || ! in_array(HasImpersonation::class, class_uses($modelClass))) {
            return false;
        }

        if (! $this->auth->check()) {
            return false;
        }

        $impersonator = $this->impersonator();

        return method_exists($impersonator, 'canImpersonate') && $impersonator->canImpersonate();
    }

    /**
     * @return Model&Authenticatable
     * @throws ImpersonateException
     */
    public function impersonator() : Authenticatable
    {
        if ($this->check()) {
            $id = $this->session->getImpersonator();
            if ($id) {
                /** @var Model&Authenticatable $user */
                $user = $this->repository->find($id);

                return $user;
            }
        }

        $user = $this->auth->user();
        if (! $user instanceof Model) {
            throw new ImpersonateException('Authenticated user is not a Model.');
        }

        return $user;
    }

    /**
     * @return Model&Authenticatable
     * @throws ImpersonateException
     */
    public function impersonated() : Authenticatable
    {
        $id = $this->session->getImpersonated();
        if (! $id) {
            throw new ImpersonateException('Not impersonating.');
        }

        /** @var Model&Authenticatable $user */
        $user = $this->repository->find($id);

        return $user;
    }

    /**
     * @param  mixed $impersonator
     * @param  mixed $impersonated
     * @return $this
     * @throws ImpersonateException
     */
    public function begin(mixed $impersonator, mixed $impersonated) : Impersonate
    {
        $impersonatorModel = $this->fetchModel($impersonator);
        $impersonatedModel = $this->fetchModel($impersonated);

        if ($this->validate($impersonatorModel, $impersonatedModel)) {
            $this->session
                ->setImpersonator($impersonatorModel)
                ->setImpersonated($impersonatedModel);

            $this->performLogin($impersonatedModel);

            event(new BeginImpersonation($impersonatorModel, $impersonatedModel));
        }

        return $this;
    }

    /**
     * @param  mixed $impersonated
     * @return $this
     * @throws ImpersonateException
     */
    public function loginAs(mixed $impersonated) : Impersonate
    {
        return $this->begin($this->impersonator(), $impersonated);
    }

    /**
     * @return Impersonate
     */
    public function leave() : Impersonate
    {
        if ($this->check()) {
            $impersonator = $this->impersonator();
            $impersonated = $this->impersonated();

            if ($this->session->flush()) {
                $this->performLogin($impersonator);

                event(new LeaveImpersonation($impersonator, $impersonated));
            }
        }

        return $this;
    }

    /**
     * @param  Model&Authenticatable $user
     * @return void
     */
    private function performLogin(Authenticatable $user) : void
    {
        if ($this->auth instanceof SessionGuard) {
            $this->auth->login($user);
            $this->setJetstreamPasswordHash($this->auth->user());
        } else {
            // Fallback to Laravel's Auth facade
            $found_session_guard = false;

            // Use server's default guard
            $defaultGuardName = config('auth.defaults.guard');
            if (is_string($defaultGuardName)) {
                $guard = AuthFacade::guard($defaultGuardName);
                if ($guard instanceof SessionGuard) {
                    $found_session_guard = true;
                    $guard->login($user);
                }
            }

            if (! $found_session_guard) {
                // Fallback to the first SessionGuard
                $guardsConfig = config('auth.guards');
                if (is_array($guardsConfig)) {
                    $guard_names = array_keys($guardsConfig);
                    foreach ($guard_names as $guard_name) {
                        if ($found_session_guard || ! is_string($guard_name)) {
                            continue;
                        }
                        $guard = AuthFacade::guard($guard_name);

                        if ($guard instanceof SessionGuard) {
                            $guard->login($user);
                            $found_session_guard = true;
                        }
                    }
                }
            }

            $this->setJetstreamPasswordHash(AuthFacade::user());
        }
    }

    /**
     * @param Authenticatable|null $user
     * @return void
     */
    private function setJetstreamPasswordHash(?Authenticatable $user) : void
    {
        if ($user && class_exists(Jetstream::class)) {
            $password = $user->getAuthPassword();
            $this->session->setPasswordHash($password);
        }
    }

    /**
     * @param  Model $impersonator
     * @param  Model $impersonated
     * @return bool
     * @throws ImpersonateException
     */
    private function validate(Model $impersonator, Model $impersonated) : bool
    {
        $uses = class_uses($impersonator);
        if (! is_array($uses) || ! in_array(HasImpersonation::class, $uses)) {
            throw new ImpersonateException(get_class($impersonator) . ' does not use ' . HasImpersonation::class);
        }

        if ($impersonator->is($impersonated)) {
            throw new ImpersonateException('You cannot impersonate yourself.');
        }

        if (! method_exists($impersonator, 'canImpersonate') || ! $impersonator->canImpersonate()) {
            throw new ImpersonateException('You don\'t have the ability to impersonate.');
        }

        if (! method_exists($impersonated, 'canBeImpersonated') || ! $impersonated->canBeImpersonated()) {
            throw new ImpersonateException('You can\'t impersonate this user.');
        }

        if (! $impersonator instanceof Authenticatable || ! $impersonated instanceof Authenticatable) {
            throw new ImpersonateException('Models must implement Authenticatable interface.');
        }

        return true;
    }

    /**
     * @param  mixed $modelOrId
     * @return Model&Authenticatable
     * @throws ImpersonateException
     */
    private function fetchModel(mixed $modelOrId) : Authenticatable
    {
        if (! $modelOrId instanceof Model) {
            $modelOrId = $this->repository->find($modelOrId);
        }

        if (! $modelOrId instanceof Authenticatable) {
            throw new ImpersonateException('Model must implement Authenticatable interface.');
        }

        return $modelOrId;
    }
}
