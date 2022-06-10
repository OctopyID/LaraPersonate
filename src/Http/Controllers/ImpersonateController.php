<?php

namespace Octopy\Impersonate\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Octopy\Impersonate\Impersonate;
use Octopy\Impersonate\ImpersonateRepository;

class ImpersonateController
{
    /**
     * @var ImpersonateRepository
     */
    protected ImpersonateRepository $repository;

    /**
     * @param  Impersonate $impersonate
     */
    public function __construct(protected Impersonate $impersonate)
    {
        $this->repository = new ImpersonateRepository($impersonate);
    }

    /**
     * @param  Request $request
     * @return Collection
     */
    public function index(Request $request) : Collection
    {
        return $this->repository->getUsers($request->get('search'));
    }
}
