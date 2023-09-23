<?php

namespace Octopy\Impersonate;

use Illuminate\Support\Facades\App;

/**
 * @return Impersonate
 */
function impersonate() : Impersonate
{
    return App::make('impersonate');
}
