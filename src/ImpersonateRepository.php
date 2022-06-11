<?php

namespace Octopy\Impersonate;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
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
     * @param  string|null $search
     * @return Collection
     */
    public function getUsers(string $search = null) : Collection
    {
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
                return $this->impersonate->impersonation()->check('impersonated', $user); // filter out users that cannot be impersonated
            })
            ->map(function ($user) {
                $val = [];
                $tmp = $user; // to avoid modifying the original object

                foreach (config('impersonate.display.fields', []) as $field) {
                    if (! str_contains($field, '.')) {
                        $val[] = $user->{$field};
                    } else {
                        // when the field is a relation, try to display the related model
                        // e.g : 'comments.user.name'
                        foreach (explode('.', $field) as $key) {
                            $tmp = $tmp instanceof Collection ? $tmp->get($key) : $tmp->$key;
                        }

                        $val[] = $tmp;
                    }
                }

                return [
                    'key' => $user->getKey(),
                    'val' => implode(config('impersonate.display.separator'), $val),
                ];
            })
            ->values();
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
        return array_merge([$this->model->getAuthIdentifierName()], config('impersonate.display.searchable', [
            //
        ]));
    }
}
