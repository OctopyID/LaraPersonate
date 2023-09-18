<?php

use Illuminate\Support\Facades\Route;
use Octopy\Impersonate\Http\Controllers\ImpersonateController;

Route::group(['prefix' => '_impersonate', 'middleware' => 'web', 'as' => 'impersonate.'], function () {
    /**
     * query:GET {
     *     search: string
     * }
     */
    Route::get('users', [ImpersonateController::class, 'index'])->name('index');
});
