<?php

namespace Octopy\Sudo\Http\Controllers;


use Octopy\Sudo\Sudo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;

/**
 * Class SudoController
 *
 * @package Octopy\Sudo\Http
 */
class SudoController extends Controller
{
    /**
     * @var Sudo
     */
    protected $sudo;

    /**
     * SudoController constructor.
     *
     * @param  Sudo  $sudo
     */
    public function __construct(Sudo $sudo)
    {
        $this->sudo = $sudo;
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function signin(Request $request) : RedirectResponse
    {
        if ($this->sudo->signin($request->userId, $request->originalUserId)) {
            return redirect()->back();
        }

        return redirect()->back();
    }
}
