<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\MetroResource\Pages;

use Fuelviews\SabHeroBlog\Models\Metro;
use Fuelviews\SabHeroBlog\Filament\Resources\MetroResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMetro extends ViewRecord
{
    protected static string $resource = MetroResource::class;

    public function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->slideOver()
                ->form(Metro::getForm()),
        ];
    }
}
