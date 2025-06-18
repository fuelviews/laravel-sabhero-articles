<?php

namespace Fuelviews\SabHeroArticle;

use Fuelviews\SabHeroArticle\Events\ArticlePublished;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class SabHeroArticleEventServiceProvider extends ServiceProvider
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
