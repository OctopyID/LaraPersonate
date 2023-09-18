<?php

namespace Octopy\Impersonate;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Repository
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     *
     */
    public function __construct()
    {
        $this->model = App::make(config('impersonate.model'));
    }

    public function get(string|null $search = null)
    {
        // TODO : Allow to search users by raw query.
        $query = $this->model->newQuery()->limit(config(
            'impersonate.interface.limit', 10
        ));

        // If trashed is true, we will add a withTrashed clause to the query
        if (config('impersonate.trashed', false) && in_array(SoftDeletes::class, class_uses_recursive($this->model))) {
            $query = $query->withTrashed();
        }

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
            ->map(function ($user) {
                return [
                    'key'   => $user->getKey(),
                    'value' => $user->name,
                ];
            })
            ->values();
    }

    /**
     * @return array
     */
    private function getColumns() : array
    {
        return array_merge([$this->model->getAuthIdentifierName()], config('impersonate.interface.searchable', [
            //
        ]));
    }
}
