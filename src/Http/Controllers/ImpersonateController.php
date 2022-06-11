<?php

namespace Octopy\Impersonate\Http\Controllers;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Octopy\Impersonate\Exceptions\ImpersonateException;
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

    /**
     * @param  Request $request
     * @return User
     * @throws ImpersonateException
     */
    public function login(Request $request)
    {
        return $this->impersonate->impersonate($this->impersonate->getImpersonator(), $request->get('user'));
    }

    /**
     * @return bool
     */
    public function leave()
    {
        return $this->impersonate->leave();
    }
}
