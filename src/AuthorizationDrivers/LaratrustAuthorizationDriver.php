<?php

namespace Octopy\LaraPersonate\AuthorizationDrivers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;

/**
 * Class LaratrustAuthorizationDriver
 *
 * @package Octopy\LaraPersonate\AuthorizationDrivers
 */
class LaratrustAuthorizationDriver extends AuthorizationDriver
{
    /**
     * @return mixed|void
     */
    public function handle()
    {
        return App::make(config('laratrust.models.role'))->get()->map(function ($role) {
            return collect(config('laratrust.user_models'))->map(function ($relationship, $key) use ($role) {
                if ($this->model instanceof $relationship) {
                    return $this->reMap(
                        $role->getMorphByUserRelation($key)->with('roles')->limit(config('impersonate.limit', 3))->get()
                    );
                }

                return [];
            })->filter(static function ($item) {
                return $item->isNotEmpty();
            });
        })->filter(static function ($item) {
            return $item->isNotEmpty();
        })->map(static function ($item) {
            return $item->collapse();
        })->collapse()->groupBy(static function ($item) {
            return Str::plural($item->roles->first()->name);
        });
    }
}
