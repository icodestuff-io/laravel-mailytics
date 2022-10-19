<?php

namespace Icodestuff\Mailytics\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Icodestuff\Mailytics\Mailytics
 */
class Mailytics extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Icodestuff\Mailytics\Mailytics::class;
    }
}
