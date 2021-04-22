<?php /** @noinspection PhpUndefinedMethodInspection */

namespace Octopy\LaraPersonate;

use Throwable;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\App;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Model;
use Octopy\LaraPersonate\Storage\Session;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class Impersonate
 * @package Octopy\LaraPersonate
 */
class Impersonate
{
    /**
     * @var string
     */
    public const VERSION = 'v2.0.0';

    /**
     * @var string
     */
    public const DISPLAY_NAME = 'name';

    /**
     * @var string
     */
    public const POSITION_LEFT = 'left';

    /**
     * @var string
     */
    public const POSITION_RIGHT = 'right';

    /**
     * @var Session
     */
    protected Session $session;

    /**
     * @var AuthManager
     */
    protected AuthManager $manager;

    /**
     * @var string|null
     */
    protected ?string $prevUserName = null;

    /**
     * @var string|null
     */
    protected ?string $nextUserName = null;

    /**
     * Impersonate constructor.
     */
    public function __construct(AuthManager $manager)
    {
        $this->manager = $manager;
        $this->session = new Session($this);
    }

    /**
     * @return bool
     */
    public function enabled() : bool
    {
        return config('impersonate.enabled', true);
    }

    /**
     * @return Model
     */
    public function getPrevUser() : Model
    {
        if (! $this->session->getPrevUserId()) {
            return $this->manager->user();
        }

        return $this->getUser($this->session->getPrevUserId());
    }

    /**
     * @return int
     */
    public function getPrevUserId() : int
    {
        return $this->session->getPrevUserId() ?? $this->manager->user()->id;
    }

    /**
     * @return string
     */
    public function getDisplayNamePrevUser() : string
    {
        if (! $this->prevUserName) {
            $this->prevUserName = $this->getPrevUser()->{config('impersonate.field.display')};
        }

        return $this->prevUserName;
    }

    /**
     * @return Model
     */
    public function getNextUser() : Model
    {
        return $this->getUser($this->session->getNextUserId());
    }

    /**
     * @return int
     */
    public function getNextUserId() : int
    {
        return $this->session->getNextUserId();
    }

    /**
     * @return string
     */
    public function getDisplayNameNextUser() : string
    {
        if (! $this->nextUserName) {
            $this->nextUserName = $this->getNextUser()->{config('impersonate.field.display')};
        }

        return $this->nextUserName;
    }

    /**
     * @param  Model|string|int $prevUser
     * @param  Model|string|int $nextUser
     * @return Model
     * @throws Throwable
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function take($prevUser, $nextUser) : Model
    {
        $prevUser = $this->getUser($prevUser);

        $nextUser = $this->getUser($nextUser);

        if (! $prevUser->canImpersonate()) {
            throw new UnauthorizedException('User does not have access to impersonate.');
        }

        if (! $nextUser->canBeImpersonated()) {
            throw new UnauthorizedException('User cannot to be impersonated.');
        }

        $this->session->saveUserId(
            $this->getUser($prevUser)->{$this->getKeyName($prevUser)},
            $this->getUser($nextUser)->{$this->getKeyName($nextUser)},
        );

        $this->manager->login($nextUser);

        return $this->getNextUser();
    }

    /**
     * @return bool
     */
    public function impersonated() : bool
    {
        return $this->session->impersonated();
    }

    /**
     * @return bool
     */
    public function authenticated() : bool
    {
        return $this->manager->check();
    }

    /**
     * @return void
     */
    public function leave()
    {
        $this->manager->login($this->getPrevUser());
        $this->session->destroy();
    }

    /**
     * @return Application|Factory|View
     */
    public function getView()
    {
        return view('impersonate::impersonate', [
            'impersonate' => $this,
        ]);
    }

    /**
     * @param  Model|string|int $user
     * @return Model
     * @noinspection PhpUndefinedMethodInspection
     */
    protected function getUser($user) : Model
    {
        if (! $user instanceof Model) {
            return $this->getModel($user)->findOrFail($user);
        }

        return $user;
    }

    /**
     * @param  Model|string|int $model
     * @return Model
     */
    protected function getModel($model) : Model
    {
        if (! $model instanceof Model) {
            $model = App::make(config('impersonate.model', User::class));
        }

        return $model;
    }

    /**
     * @param  Model|string|int $model
     * @return string
     */
    protected function getKeyName($model) : string
    {
        return $this->getModel($model)->getKeyName();
    }
}
