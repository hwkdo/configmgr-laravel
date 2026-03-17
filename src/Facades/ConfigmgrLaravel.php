<?php

namespace Hwkdo\ConfigmgrLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hwkdo\ConfigmgrLaravel\ConfigmgrLaravel
 */
class ConfigmgrLaravel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Hwkdo\ConfigmgrLaravel\ConfigmgrLaravel::class;
    }
}
