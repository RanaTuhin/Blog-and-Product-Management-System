<?php

declare(strict_types=1);
use App\Microsoft\MicrosoftServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    MicrosoftServiceProvider::class,
];
