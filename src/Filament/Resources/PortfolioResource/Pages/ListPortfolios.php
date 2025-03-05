<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\PortfolioResource\Pages;

use Fuelviews\SabHeroBlog\Filament\Resources\PortfolioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPortfolios extends ListRecords
{
    protected static string $resource = PortfolioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
