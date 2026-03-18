<?php

declare(strict_types=1);

use App\Http\Controllers\OauthController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): Factory|\Illuminate\Contracts\View\View => view('welcome'));

/* Microsoft OAuth */
Route::middleware(['auth:admin'])
    ->group(function () {
        Route::get('/oauth/microsoft/redirect', [OauthController::class, 'redirectMicrosoft'])
            ->name('oauth.microsoft.redirect');

        Route::get('/microsoft/callback', [OauthController::class, 'callbackMicrosoft'])
            ->name('oauth.microsoft.callback');
    });
