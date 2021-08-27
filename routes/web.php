<?php

use Illuminate\Support\Facades\Route;
use Octopy\LaraPersonate\Http\Controllers\ImpersonateController;

Route::group(['prefix' => 'impersonate', 'as' => 'impersonate.', 'middleware' => 'web'], function () {
    # :/impersonate/list
    Route::get('list', [ImpersonateController::class, 'list'])->name('list');

    # :/impersonate/signin
    Route::post('signin', [ImpersonateController::class, 'signin'])->name('signin');

    # :/impersonate/logout
    Route::post('logout', [ImpersonateController::class, 'logout'])->name('logout');
});
