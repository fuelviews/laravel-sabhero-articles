<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\PortfolioResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Fuelviews\SabHeroBlog\Filament\Resources\PortfolioResource;

class CreatePortfolio extends CreateRecord
{
    protected static string $resource = PortfolioResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
