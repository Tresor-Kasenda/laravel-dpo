<?php

namespace Scott\LaravelDpo;

use Illuminate\Support\Facades\Facade;

class LaravelDPOFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-dpo';
    }
}
