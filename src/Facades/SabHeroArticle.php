<?php

namespace Fuelviews\SabHeroArticles\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Fuelviews\SabHeroArticles\SabHeroArticles
 */
class SabHeroArticles extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Fuelviews\SabHeroArticles\SabHeroArticles::class;
    }
}
