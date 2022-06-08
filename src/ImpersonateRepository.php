<?php

namespace Octopy\Impersonate;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;

class ImpersonateRepository
{
    /**
     * @var User|mixed
     */
    protected User $model;

    /**
     * @param  Impersonate $impersonate
     */
    public function __construct(protected Impersonate $impersonate)
    {
        $this->model = App::make(config('impersonate.model'));
    }

    /**
     * @return User
     */
    public function getImpersonatorInStorage() : User
    {
        return $this->model->where($this->model->getAuthIdentifierName(), $this->impersonate->storage()->getImpersonatorIdentifier())->first();
    }

    /**
     * @return User
     */
    public function getImpersonatedInStorage() : User
    {
        return $this->model->where($this->model->getAuthIdentifierName(), $this->impersonate->storage()->getImpersonatedIdentifier())->first();
    }
}
