<?php

namespace Fuelviews\SabHeroBlog\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Fuelviews\SabHeroBlog\SabHeroBlog
 */
class SabHeroBlog extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Fuelviews\SabHeroBlog\SabHeroBlog::class;
    }
}
