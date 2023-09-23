<?php

use Illuminate\Support\Facades\Route;
use Octopy\Impersonate\Http\Controllers\ImpersonateController;

Route::middleware(['web', 'auth'])->prefix('_impersonate')->as('impersonate.')->group(function ($route) {
    /**
     * query:GET {
     *     search: string
     * }
     */
    $route->get('users', [ImpersonateController::class, 'index'])->name('index');

    /**
     * body:POST {
     *     user: int|string
     * }
     */
    $route->post('login', [ImpersonateController::class, 'login'])->name('login');

    /**
     * body:POST {
     *
     * }
     */
    $route->post('leave', [ImpersonateController::class, 'leave'])->name('leave');
});
