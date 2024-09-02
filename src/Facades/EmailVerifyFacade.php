<?php

namespace ErlandMuchasaj\LaravelEmailVerify\Facades;

use Illuminate\Support\Facades\Facade;

class EmailVerifyFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-email-verify';
    }
}
