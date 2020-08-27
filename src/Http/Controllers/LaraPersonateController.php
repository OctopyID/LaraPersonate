<?php

namespace Octopy\LaraPersonate\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Octopy\LaraPersonate\LaraPersonate;

/**
 * Class LaraPersonateController
 *
 * @package Octopy\LaraPersonate\Http\Controllers
 */
class LaraPersonateController extends Controller
{
    /**
     * @var LaraPersonate
     */
    protected $personate;

    /**
     * LaraPersonateController constructor.
     *
     * @param  LaraPersonate  $personate
     */
    public function __construct(LaraPersonate $personate)
    {
        $this->personate = $personate;
    }

    /**
     * @return array|mixed
     */
    public function getUsers()
    {
        try {
            return $this->personate->getUsers();
        } catch (Exception $exception) {
            return [];
        }
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function trySignin(Request $request) : RedirectResponse
    {
        try {
            $this->personate->signin($request->userId, $request->originalId);
        } catch (Exception $exception) {
        }

        return redirect()->back();
    }

    /**
     * @return RedirectResponse
     */
    public function trySignout() : RedirectResponse
    {
        try {
            $this->personate->signout();
        } catch (Exception $exception) {
        }

        return redirect()->back();
    }
}
