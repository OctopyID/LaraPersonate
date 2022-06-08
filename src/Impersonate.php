<?php

namespace Octopy\Impersonate;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Octopy\Impersonate\Contracts\Storage;
use Octopy\Impersonate\Exceptions\ImpersonateException;
use Octopy\Impersonate\Storage\SessionStorage;
use ReflectionClass;
use Throwable;

class Impersonate
{
    /**
     * @var Storage
     */
    protected Storage $storage;

    /**
     * @var StatefulGuard
     */
    protected StatefulGuard $guard;

    /**
     * @var ImpersonateRepository
     */
    protected ImpersonateRepository $repository;

    /**
     * @var array<string, Closure<User>>
     */
    protected array $criteria = [
        'impersonator' => false,
        'impersonated' => false,
    ];

    /**
     * Impersonate constructor.
     */
    public function __construct()
    {
        // Set default criteria.
        $this->criteria(fn() => true, fn() => true);

        // Set auth guard.
        $this->guard(config(
            'impersonate.guard', 'web'
        ));

        // Set storage.
        $this->storage(config(
            'impersonate.storage', 'session'
        ));

        $this->repository = new ImpersonateRepository($this);
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
     * Set criteria to check if impersonation is allowed.
     *
     * @param  Closure|bool $impersonator
     * @param  Closure|bool $impersonated
     * @return Impersonate
     */
    public function criteria(Closure|bool $impersonator = true, Closure|bool $impersonated = true) : self
    {
        // @codeCoverageIgnoreStart
        if (! $impersonator instanceof Closure) {
            $impersonator = function (User $user) use ($impersonator) {
                return $impersonator;
            };
        }

        if (! $impersonated instanceof Closure) {
            $impersonated = function (User $user) use ($impersonated) {
                return $impersonated;
            };
        }
        // @codeCoverageIgnoreEnd

        $this->criteria = [
            'impersonator' => $impersonator,
            'impersonated' => $impersonated,
        ];

        return $this;
    }

    /**
     * Impersonate user.
     *
     * @param  Authenticatable $impersonator
     * @param  Authenticatable $impersonated
     * @return void
     * @throws ImpersonateException
     */
    public function impersonate(Authenticatable $impersonator, Authenticatable $impersonated) : void
    {
        // when in impersonation mode, $impersonator set to current impersonator
        if ($this->storage->isInImpersonatingMode()) {
            $impersonator = $this->getImpersonator();
        }

        // check if impersonation is allowed
        if ($this->check($impersonator, $impersonated)) {
            $this->storage
                ->setImpersonatorIdentifier($impersonator)
                ->setImpersonatedIdentifier($impersonated);

            $this->guard->login($impersonated);
        }
    }

    /**
     * Get current authenticated user.
     *
     * @return Authenticatable|User
     */
    public function getCurrentUser() : Authenticatable|User
    {
        return $this->guard->user();
    }

    /**
     * Get current impersonator.
     *
     * @return User
     */
    public function getImpersonator() : User
    {
        return $this->repository->getImpersonatorInStorage();
    }

    /**
     * Get current impersonated user.
     *
     * @return User
     */
    public function getImpersonated() : User
    {
        return $this->repository->getImpersonatedInStorage();
    }

    /**
     * Set or get storage.
     *
     * @param  Storage|string|null $storage
     * @return Storage
     */
    public function storage(Storage|string $storage = null) : Storage
    {
        if (is_null($storage)) {
            return $this->storage;
        }

        // try to find the storage class
        if (is_string($storage) && ! class_exists($storage)) {
            try {
                $storage = match (strtolower($storage)) {
                    'session' => SessionStorage::class,
                };
            } catch (Throwable) {
                //
            }
        }

        return $this->storage = ! is_string($storage) ? $storage : App::make($storage);
    }

    /**
     * Check if impersonation is allowed.
     *
     * @param  Authenticatable $impersonator
     * @param  Authenticatable $impersonated
     * @return bool
     * @throws ImpersonateException
     */
    private function check(Authenticatable $impersonator, Authenticatable $impersonated) : bool
    {
        if ($impersonator->getAuthIdentifier() === $impersonated->getAuthIdentifier()) {
            throw new ImpersonateException('You cannot impersonate yourself.');
        }

        if (! $this->criteria['impersonator']($impersonator)) {
            throw new ImpersonateException('You don\'t have the ability to impersonate.');
        }

        if (! $this->criteria['impersonated']($impersonated)) {
            throw new ImpersonateException('You can\'t impersonate this user.');
        }

        return true;
    }
}
