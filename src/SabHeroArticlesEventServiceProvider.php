<?php

namespace Fuelviews\SabHeroArticles;

use Fuelviews\SabHeroArticles\Events\ArticlePublished;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class SabHeroArticlesEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ArticlePublished::class => [
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
