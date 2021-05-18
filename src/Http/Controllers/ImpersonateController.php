<?php

namespace Octopy\LaraPersonate\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Octopy\LaraPersonate\Impersonate;
use Throwable;

/**
 * Class ImpersonateController
 * @package Octopy\LaraPersonate\Http\Controllers
 */
class ImpersonateController extends Controller
{
    /**
     * @var Impersonate
     */
    protected Impersonate $impersonate;

    /**
     * ImpersonateController constructor.
     * @param  Impersonate $impersonate
     */
    public function __construct(Impersonate $impersonate)
    {
        $this->impersonate = $impersonate;
    }

    /**
     * @param  Request $request
     * @return Collection
     */
    public function list(Request $request) : Collection
    {
        $query = App::make(config('impersonate.model'));

        if ($request->has('search')) {
            $query = $query->where(function ($query) use ($request) {
                foreach (config('impersonate.field.search_keys', []) as $field) {
                    $query->orWhere($field, 'LIKE', '%' . $request->get('search') . '%');
                }
            });
        }

        $query = $query->get()->filter(function ($user) {
            return $user->canBeImpersonated();
        });

        if (config('impersonate.limit') > 0) {
            $query = $query->forPage(0, config('impersonate.limit'));
        }

        return $query->map(function ($user) {
            return [
                'id'   => $user->{$user->getKeyName()},
                'text' => $user->{config('impersonate.field.display', Impersonate::DISPLAY_NAME)},
            ];
        })
            ->values();
    }

    /**
     * @param  Request $request
     * @return Model
     * @throws Throwable
     */
    public function signin(Request $request) : Model
    {
        return $this->impersonate->take($request->get('user'), $request->get('take'));
    }

    /**
     * @return void
     */
    public function logout()
    {
        $this->impersonate->leave();
    }
}
