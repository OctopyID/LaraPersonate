<?php

namespace Octopy\LaraPersonate\Storage;

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
     * @param  int $prevUser
     * @param  int $nextUser
     * @return Session
     */
    public function saveUserId(int $prevUser, int $nextUser) : Session
    {
        session([
            'impersonate' => [
                'prev_user' => encrypt($prevUser),
                'next_user' => encrypt($nextUser),
            ],
        ]);

        return $this;
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
