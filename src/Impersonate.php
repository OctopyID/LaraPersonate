<?php

namespace Octopy\LaraPersonate;

use Throwable;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Octopy\LaraPersonate\Storage\Session;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\UnauthorizedException;

/**
 * Class Impersonate
 * @package Octopy\LaraPersonate
 */
class Impersonate
{
    /**
     * @var string
     */
    public const FIELD_NAME = 'name';

    /**
     * @var Session
     */
    protected Session $session;

    /**
     * Impersonate constructor.
     */
    public function __construct()
    {
        $this->session = new Session($this);
    }

    /**
     * @return bool
     */
    public function enabled() : bool
    {
        return config('impersonate.enabled', true);
    }

    /**
     * @param  Model|string|int $prevUser
     * @param  Model|string|int $nextUser
     * @return Authenticatable
     * @throws Throwable
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function take($prevUser, $nextUser) : Authenticatable
    {
        $prevUser = $this->getUser($prevUser);
        $nextUser = $this->getUser($nextUser);

        if (! $nextUser->canBeImpersonated()) {
            throw new UnauthorizedException('User cannot to be impersonated.');
        }

        if (! $prevUser->canImpersonate()) {
            throw new UnauthorizedException('User does not have access to impersonate.');
        }

        return Auth::loginUsingId(
            $this->session->saveUserId(
                $this->getUser($prevUser)->{$this->getKeyName($prevUser)},
                $this->getUser($nextUser)->{$this->getKeyName($nextUser)},
            )
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

    /**
     * @param  Model|string|int $user
     * @return Model
     * @noinspection PhpUndefinedMethodInspection
     */
    protected function getUser($user)
    {
        $model = $this->getModel($user);

        if (! $user instanceof Model) {
            $user = $model->findOrFail($user);
        }

        return $user;
    }

    /**
     * @param  Model|string|int $model
     * @return Model
     */
    protected function getModel($model) : Model
    {
        if (! $model instanceof Model) {
            $model = App::make(config('impersonate.model', User::class));
        }

        return $model;
    }

    /**
     * @param  Model|string|int $model
     * @return string
     */
    protected function getKeyName($model) : string
    {
        return $this->getModel($model)->getKeyName();
    }
}
