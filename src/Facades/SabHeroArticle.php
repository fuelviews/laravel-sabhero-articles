<?php

namespace Fuelviews\SabHeroArticle\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Fuelviews\SabHeroArticle\SabHeroArticle
 */
class SabHeroArticle extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Fuelviews\SabHeroArticle\SabHeroArticle::class;
    }
}
