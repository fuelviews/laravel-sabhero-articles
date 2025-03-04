<?php

namespace Fuelviews\SabHeroBlog;

use Fuelviews\SabHeroBlog\Events\BlogPublished;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class SabHeroBlogEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BlogPublished::class => [
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
