<?php

namespace App\Microsoft;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class MicrosoftServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('microsoft', function (Application $app) {
            return new OauthClient;
        });

        $this->app->singleton('mail', function (Application $app) {
            return new OauthClient;
        });

    }

    public function boot(): void {}
}
