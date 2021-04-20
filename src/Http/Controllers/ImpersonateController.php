<?php

namespace Octopy\LaraPersonate\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Octopy\LaraPersonate\Impersonate;

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
     */
    public function signin(Request $request)
    {
        //
    }

    /**
     * @return void
     */
    public function logout()
    {
        //
    }
}
