<?php

namespace App\Microsoft\Facades;

use Illuminate\Support\Facades\Facade;

class Microsoft extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'microsoft';
    }
}
