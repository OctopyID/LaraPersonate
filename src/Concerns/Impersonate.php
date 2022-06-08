<?php

namespace Octopy\Impersonate\Concerns;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\App;

trait Impersonate
{
    /**
     * @param  Authenticatable $user
     * @return mixed
     */
    public function impersonate(Authenticatable $user) : mixed
    {
        return App::make('impersonate')->impersonate($this, $user);
    }
}
