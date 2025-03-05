<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\PortfolioResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Fuelviews\SabHeroBlog\Filament\Resources\PortfolioResource;
use Fuelviews\SabHeroBlog\Models\Portfolio;

class ViewPortfolio extends ViewRecord
{
    protected static string $resource = PortfolioResource::class;

    public function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->form(Portfolio::getForm()),
        ];
    }
}
