<?php

namespace Octopy\Sudo;

use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class Sudo
 *
 * @package Octopy\Sudo
 */
class Sudo
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Authenticatable
     */
    protected $auth;

    /**
     * Sudo constructor.
     *
     * @param  Application       $app
     * @param  AuthManager|null  $auth
     */
    public function __construct(Application $app, AuthManager $auth = null)
    {
        $this->app = $app;
        $this->auth = $auth;
    }

    /**
     * @param  int  $userId
     * @param  int  $originalUserId
     * @return Authenticatable|false
     */
    public function signin(int $userId, int $originalUserId)
    {
        $this->app->session->put([
            'octopyid.sudo.has_sudoed'  => true,
            'octopyid.sudo.original_id' => $originalUserId,
        ]);

        return $this->auth->loginUsingId($userId);
    }
    
    /**
     * @param  Request   $request
     * @param  Response  $response
     * @return Response
     * @throws BindingResolutionException
     */
    public function modifyResponse(Request $request, Response $response) : Response
    {
        if (! $this->auth->check()) {
            return $response;
        }

        $sudo = view('sudo::selector', [
            'users'        => $this->getUsers(),
            'hasSudoed'    => $this->hasSudoed(),
            'currentUser'  => $this->auth->user(),
            'originalUser' => $this->getOriginalUser(),
        ]);

        return $response->setContent($response->getContent() . $sudo);
    }

    /**
     * @return bool
     */
    public function isEnabled() : bool
    {
        return config('sudo.enabled');
    }

    /**
     * @return mixed
     * @throws BindingResolutionException
     */
    private function getOriginalUser()
    {
        if (! $this->hasSudoed()) {
            return $this->auth->user();
        }

        return $this->app->make(config('sudo.user_model'))->find(
            $this->app->session->get('octopyid.sudo.original_id')
        );
    }

    /**
     * @return mixed
     * @throws BindingResolutionException
     */
    private function getUsers()
    {
        $model = $this->app->make(config('sudo.user_model'));

        if (config('sudo.max_shown') === -1) {
            return $model->get();
        }

        return $model->limit(config('sudo.max_shown'))->get();
    }

    /**
     * @return mixed
     */
    private function hasSudoed()
    {
        return $this->app->session->has('octopyid.sudo.has_sudoed');
    }
}
