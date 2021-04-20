<?php

namespace Octopy\LaraPersonate\Storage;

use Octopy\LaraPersonate\Impersonate;

/**
 * Class SessionStorage
 * @package Octopy\LaraPersonate\StorageTest
 */
class Session implements Contract
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
                'prev_user' => encrypt($prevUser),
                'next_user' => encrypt($nextUser),
            ],
        ]);

        return $nextUser;
    }

    /**
     * @return int
     */
    public function getPrevUserId() : int
    {
        return (session('impersonate'));
    }

    /**
     * @param  bool $status
     * @return bool
     */
    public function impersonated(bool $status = false) : bool
    {
        if ($status) {
            return session([
                'impersonate.impersonated' => $status,
            ]);
        }

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
