<?php

namespace Octopy\Impersonate;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class Repository
{
    /**
     * @var Model
     */
    private Model $model;

    /**
     * Repository constructor.
     */
    public function __construct()
    {
        $this->model = App::make(config(
            'impersonate.model'
        ));
    }

    /**
     * @param  mixed $id
     * @return Model
     */
    public function find(mixed $id) : Model
    {
        return $this->model->find($id);
    }

    /**
     * @param  string|null $search
     * @return Paginator
     * @noinspection PhpUndefinedMethodInspection
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function get(string|null $search = null) : Paginator
    {
        $query = $this->model->newQuery();

        // if trashed is true, we will add a withTrashed clause to the query
        if (config('impersonate.trashed', false) && in_array(SoftDeletes::class, class_uses_recursive($this->model))) {
            $query = $query->withTrashed();
        }

        // if search is not null, we will add a where clause to the query
        $query->when($search, function ($query) use ($search) {
            foreach ($this->model->getImpersonateSearchField() as $column) {
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
        });

        return $query->simplePaginate(perPage: 20);
    }
}
