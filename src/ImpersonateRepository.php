<?php

namespace Octopy\Impersonate;

use Illuminate\Database\Eloquent\Collection;
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
     * @return Collection
     */
    public function getUsers() : Collection
    {
        return $this->model->select($this->getColumns())->get()->filter(function ($user) {
            return $user->canBeImpersonated();
        });
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

    /**
     * @return array
     */
    private function getColumns() : array
    {
        return array_merge([$this->model->getAuthIdentifierName()], config('impersonate.field.columns', [
            //
        ]));
    }
}
