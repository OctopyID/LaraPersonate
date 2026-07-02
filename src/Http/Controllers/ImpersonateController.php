<?php

namespace Octopy\Impersonate\Http\Controllers;

use Illuminate\Http\Request;
use Octopy\Impersonate\Exceptions\ImpersonateException;
use Octopy\Impersonate\Http\Resources\ImpersonateCollection;
use Octopy\Impersonate\Impersonate;
use Octopy\Impersonate\Repository;

class ImpersonateController
{
    /**
     * @param  Impersonate $impersonate
     * @param  Repository  $repository
     */
    public function __construct(protected Impersonate $impersonate, protected Repository $repository)
    {
        //
    }

    /**
     * @param  Request $request
     * @return ImpersonateCollection
     */
    public function index(Request $request) : ImpersonateCollection
    {
        $query = $request->get('query');

        return new ImpersonateCollection($this->repository->get(is_string($query) ? $query : null));
    }

    /**
     * @param  Request $request
     * @throws ImpersonateException
     */
    public function login(Request $request) : void
    {
        $this->impersonate->begin($this->impersonate->impersonator(), $request->get('user'));
    }

    /**
     * @return void
     */
    public function leave() : void
    {
        $this->impersonate->leave();
    }
}
