<?php

namespace Octopy\Impersonate\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
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
     * @return Collection
     */
    public function index()
    {
        return $this->repository->getUsers();
    }
}
