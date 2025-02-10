<?php

namespace Fuelviews\SabHeroBlog\Filament\Pages;

use Illuminate\Contracts\Support\Htmlable;
use ShuvroRoy\FilamentSpatieLaravelHealth\Pages\HealthCheckResults as BaseHealthCheckResults;

class HealthCheckResults extends BaseHealthCheckResults
{
    protected static ?int $navigationSort = 10;


    public function getHeading(): string|Htmlable
    {
        return 'Health Check Results';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Application';
    }

    public static function getNavigationLabel(): string
    {
        return 'App Health';
    }
}
