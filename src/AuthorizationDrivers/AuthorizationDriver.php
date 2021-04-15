<?php

namespace Octopy\LaraPersonate\AuthorizationDrivers;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AuthorizationDriver
 *
 * @package Octopy\LaraPersonate\AuthorizationDrivers
 */
abstract class AuthorizationDriver
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * AuthorizationDriver constructor.
     *
     * @param  Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    abstract public function handle();

    /**
     * @param  Collection $collection
     * @return Collection
     */
    protected function reMap(Collection $collection) : Collection
    {
        [$id, $name] = array_values(config('impersonate.fields'));
        if ($id !== 'id' || $name !== 'name') {
            return $collection->map(static function ($user) use ($id, $name) {
                $user->id = $user->$id;
                $user->name = $user->$name;

                return $user;
            });
        }

        return $collection;
    }
}
