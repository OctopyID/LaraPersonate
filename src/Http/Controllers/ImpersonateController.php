<?php

namespace Octopy\Impersonate\Http\Controllers;

use Octopy\Impersonate\Repository;

class ImpersonateController
{
    /**
     * @param  Repository $repository
     */
    public function __construct(protected Repository $repository)
    {
        //
    }

    public function index()
    {
        return $this->repository->get();
    }
}
