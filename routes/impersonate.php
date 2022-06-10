<?php

use Illuminate\Support\Facades\Route;
use Octopy\Impersonate\Http\Controllers\ImpersonateController;

Route::group(['prefix' => 'impersonate', 'as' => 'impersonate.'], function () {
    Route::get('users', [ImpersonateController::class, 'index'])->name('index');
});
