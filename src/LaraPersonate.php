<?php

namespace Octopy\LaraPersonate;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\Eloquent\Model;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Container\BindingResolutionException;
use Octopy\LaraPersonate\AuthorizationDrivers\DefaultAuthorizationDriver;
use Octopy\LaraPersonate\AuthorizationDrivers\LaratrustAuthorizationDriver;

/**
 * Class LaraPersonate
 *
 * @package Octopy\LaraPersonate
 */
class LaraPersonate
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
     * @var string[]
     */
    protected $authorizationDrivers = [
        LaratrustUserTrait::class => LaratrustAuthorizationDriver::class,
    ];

    /**
     * LaraPersonate constructor.
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
     * @param  Response  $response
     * @return Response
     * @throws BindingResolutionException
     */
    public function modifyResponse(Response $response) : Response
    {
        if (! $this->auth->check()) {
            return $response;
        }

        $content = $response->getContent();

        $impersonate = view('impersonate::impersonate', [
            'hasSigned'    => $this->hasSigned(),
            'currentUser'  => $this->auth->user(),
            'originalUser' => $this->getOriginalUser(),
        ]);

        $impersonate = preg_replace('/>\s+</m', '><', preg_replace('/\n/', '', $impersonate));

        $position = strripos($content, '</body>');
        if ($position !== false) {
            $content = substr($content, 0, $position) . $impersonate . substr($content, $position);
        } else {
            $content .= $impersonate;
        }

        return $response->setContent($content);
    }

    /**
     * @return bool
     */
    private function hasSigned() : bool
    {
        return $this->app->session->has('LaraPersonate.hasSigned');
    }

    /**
     * @return mixed
     * @throws BindingResolutionException
     */
    private function getOriginalUser()
    {
        if (! $this->hasSigned()) {
            return $this->auth->user();
        }

        return $this->app->make(config('impersonate.user_model'))->find(
            $this->app->session->get('LaraPersonate.myUserID')
        );
    }

    /**
     * @return bool
     */
    public function isEnabled() : bool
    {
        return config('impersonate.enabled', false);
    }

    /**
     * @param  Request  $request
     * @return bool
     */
    public function personateRequest(Request $request) : bool
    {
        return $request->segment(1) === '_impersonate';
    }

    /**
     * @param  int  $userId
     * @param  int  $myUserId
     * @return void
     */
    public function signin(int $userId, int $myUserId) : void
    {
        if ($userId !== $myUserId) {

            $this->app->session->put([
                'LaraPersonate.hasSigned' => true,
                'LaraPersonate.myUserID'  => $myUserId,
            ]);

            $this->auth->loginUsingId($userId);
        } else {
            $this->signout();
        }
    }

    /**
     * @return void
     */
    public function signout() : void
    {
        $this->auth->loginUsingId(
            $this->app->session->get('LaraPersonate.myUserID')
        );

        $this->app->session->forget('LaraPersonate');
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        try {
            $model = $this->app->make(config('impersonate.user_model'));

            return $this->app->makeWith(
                $this->getAuthorizationDriver($model), compact('model')
            )->handle();
        } catch (BindingResolutionException $exception) {
        } catch (Exception $exception) {
        }

        return [];
    }

    /**
     * @param  Model  $model
     * @return string
     */
    private function getAuthorizationDriver(Model $model) : ?string
    {
        if (config('impersonate.with_roles', false)) {
            $classes = class_uses($model);
            foreach ($this->authorizationDrivers as $package => $driver) {
                if (isset($classes[$package])) {
                    return $driver;
                }
            }
        }

        return DefaultAuthorizationDriver::class;
    }
}
