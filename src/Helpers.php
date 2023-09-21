<?php

namespace Octopy\Impersonate;

use Illuminate\Support\Facades\App;

if (! function_exists('impersonate')) {
    /**
     * @return Impersonate
     */
    function impersonate() : Impersonate
    {
        return App::make(Impersonate::class);
    }
}
