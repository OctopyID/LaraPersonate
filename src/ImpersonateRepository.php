<?php

namespace Octopy\Impersonate;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Octopy\Impersonate\Support\TextDisplay;

class ImpersonateRepository
{
    /**
     * @var User|mixed
     */
    protected User $model;

    /**
     * @param  ImpersonateManager $impersonate
     */
    public function __construct(protected ImpersonateManager $impersonate)
    {
        $this->model = App::make(config('impersonate.model'));
    }

    /**
     * @param  int|string $impersonator
     * @return User
     */
    public function findUser(int|string $impersonator) : User
    {
        return $this->model->where($this->model->getAuthIdentifierName(), $impersonator)->first();
    }

    /**
     * @param  string|null $search
     * @return Collection
     */
    public function getUsers(string $search = null) : Collection
    {
        // TODO : Allow to search users by raw query.

        $query = $this->model->newModelQuery()->limit(config(
            'impersonate.display.limit', 10
        ));

        // If search is not null, we will add a where clause to the query
        if ($search) {
            foreach ($this->getColumns() as $column) {
                if (! str_contains($column, '.')) {
                    $query->orWhere($column, 'LIKE', "%{$search}%");
                } else {
                    // when the field is a relation, try to search the related model
                    $fields = explode('.', $column);
                    $column = array_pop($fields);

                    $query->orWhereHas(implode('.', $fields), function ($query) use ($column, $search) {
                        $query->where($column, 'LIKE', "%{$search}%");
                    });
                }
            }
        }

        return $query->get()
            ->filter(function ($user) {
                return $this->impersonate->authorization()->check('impersonated', $user); // filter out users that cannot be impersonated
            })
            ->map(function ($user) {
                $display = new TextDisplay($user);

                return [
                    'key' => $user->getKey(),
                    'val' => $display->displayTextByFields(),
                ];
            })
            ->values();
    }

    /**
     * @return User
     */
    public function getImpersonatorInStorage() : User
    {
        return $this->findUser($this->impersonate->storage()->getImpersonatorIdentifier());
    }

    /**
     * @return User
     */
    public function getImpersonatedInStorage() : User
    {
        return $this->findUser($this->impersonate->storage()->getImpersonatedIdentifier());
    }

    /**
     * @return array
     */
    private function getColumns() : array
    {
        return array_merge([$this->model->getAuthIdentifierName()], config('impersonate.display.searchable', [
            //
        ]));
    }
}
