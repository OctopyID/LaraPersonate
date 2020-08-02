<?php

namespace Octopy\Sudo;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as BaseServiceProvider;

/**
 * Class RouteServiceProvider
 *
 * @package Octopy\Sudo
 */
class RouteServiceProvider extends BaseServiceProvider
{
    /**
     * @return void
     */
    public function map() : void
    {
        Route::group(['prefix' => 'octopyid', 'namespace' => 'Octopy\Sudo\Http\Controllers', 'middleware' => 'web'], static function () {
            Route::post('sudo/signin', 'SudoController@signin')->name('sudo.signin');
        });
    }
}
