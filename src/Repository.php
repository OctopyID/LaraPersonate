<?php

namespace Octopy\Impersonate;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use RuntimeException;

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
        $modelClass = config('impersonate.model');
        if (! is_string($modelClass)) {
            throw new RuntimeException('impersonate.model configuration must be a string.');
        }

        $this->model = App::make($modelClass);
    }

    /**
     * @param  mixed $id
     * @return Model
     */
    public function find(mixed $id) : Model
    {
        $result = $this->model->newQuery()->findOrFail($id);

        if (! $result instanceof Model) {
            throw new RuntimeException('Model not found');
        }

        return $result;
    }

    /**
     * @param  string|null $search
     * @return Paginator<Model>
     */
    public function get(?string $search = null) : Paginator
    {
        /** @var Builder<Model> $query */
        $query = $this->model->newQuery();

        // if trashed is true, we will add a withTrashed clause to the query
        $uses = class_uses_recursive($this->model);
        if (config('impersonate.trashed', false) && is_array($uses) && in_array(SoftDeletes::class, $uses)) {
            /** @phpstan-ignore-next-line */
            $query = $query->withTrashed();
        }

        // if search is not null, we will add a where clause to the query
        if ($search) {
            $query->where(function (Builder $query) use ($search) {
                if (method_exists($this->model, 'getImpersonateSearchField')) {
                    $fields = $this->model->getImpersonateSearchField();
                    if (is_array($fields)) {
                        foreach ($fields as $column) {
                            if (is_string($column)) {
                                if (! str_contains($column, '.')) {
                                    $query->orWhere($column, 'LIKE', "%{$search}%");
                                } else {
                                    // when the field is a relation, try to search the related model
                                    $relationFields = explode('.', $column);
                                    $relatedColumn = array_pop($relationFields);

                                    $query->orWhereHas(implode('.', $relationFields), function (Builder $query) use ($relatedColumn, $search) {
                                        $query->where($relatedColumn, 'LIKE', "%{$search}%");
                                    });
                                }
                            }
                        }
                    }
                }
            });
        }

        return $query->simplePaginate(perPage: 20);
    }
}
