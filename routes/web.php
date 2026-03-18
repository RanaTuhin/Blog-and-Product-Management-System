<?php

declare(strict_types=1);

use App\Http\Microsoft\Controllers\OAuthController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): Factory|\Illuminate\Contracts\View\View => view('welcome'));

/* Microsoft OAuth */
Route::middleware(['auth:admin'])
    ->group(function () {
        Route::get('/oauth/microsoft/redirect', [OAuthController::class, 'redirectMicrosoft'])
            ->name('oauth.microsoft.redirect');

        Route::get('/oauth/microsoft/callback', [OAuthController::class, 'callbackMicrosoft'])
            ->name('oauth.microsoft.callback');
    });
