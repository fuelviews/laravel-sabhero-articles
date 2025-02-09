<?php

namespace Fuelviews\SabHeroBlog;

use Fuelviews\SabHeroBlog\Commands\SabHeroBlogCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SabHeroBlogServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-sabhero-blog')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_sabhero_blog_table')
            ->hasCommand(SabHeroBlogCommand::class);
    }
}
