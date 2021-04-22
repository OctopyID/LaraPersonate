<?php /** @noinspection PhpUndefinedFieldInspection */

namespace Octopy\LaraPersonate\Storage;

use Octopy\LaraPersonate\Impersonate;

/**
 * Class SessionStorage
 * @package Octopy\LaraPersonate\StorageTest
 */
class Session
{
    /**
     * @var array
     */
    protected array $data = [];

    /**
     * @var Impersonate
     */
    protected Impersonate $impersonate;

    /**
     * Session constructor.
     * @param  Impersonate $impersonate
     */
    public function __construct(Impersonate $impersonate)
    {
        $this->impersonate = $impersonate;
    }

    /**
     * @param  int|string $prevUser
     * @param  int|string $nextUser
     * @return int|string
     */
    public function saveUserId(int $prevUser, int $nextUser)
    {
        session([
            'impersonate' => [
                'impersonated' => true,
                'prev_user'    => $prevUser,
                'next_user'    => $nextUser,
            ],
        ]);

        return $nextUser;
    }

    /**
     * @return int|null
     */
    public function getPrevUserId() : ?int
    {
        return session('impersonate.prev_user');
    }

    /**
     * @return  int|null
     */
    public function getNextUserId() : ?int
    {
        return session('impersonate.next_user');
    }

    /**
     * @return bool
     */
    public function impersonated() : bool
    {
        return session('impersonate.impersonated', false);
    }

    /**
     * @return void
     */
    public function destroy() : void
    {
        session()->forget('impersonate');
    }
}
