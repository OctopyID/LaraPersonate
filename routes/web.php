<?php

use Illuminate\Support\Facades\Route;
use Octopy\LaraPersonate\Http\Controllers\ImpersonateController;

Route::group(['prefix' => 'impersonate', 'as' => 'impersonate.'], function () {
    # :/impersonate/signin
    Route::post('signin', [ImpersonateController::class, 'signin'])->name('signin');

    # :/impersonate/logout
    Route::post('logout', [ImpersonateController::class, 'logout'])->name('logout');
});
