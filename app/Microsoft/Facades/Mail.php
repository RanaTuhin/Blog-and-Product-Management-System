<?php

namespace App\Microsoft\Facades;

use Illuminate\Support\Facades\Facade;

class Mail extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'mail';
    }
}
