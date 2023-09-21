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

    /**
     * body:POST {
     *     user: int|string
     * }
     */
    Route::post('login', [ImpersonateController::class, 'login'])->name('login');

    /**
     * body:POST {
     *
     * }
     */
    Route::post('leave', [ImpersonateController::class, 'leave'])->name('leave');
});
