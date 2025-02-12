<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\MetroResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Fuelviews\SabHeroBlog\Filament\Resources\MetroResource;
use Fuelviews\SabHeroBlog\Models\Metro;

class ViewMetro extends ViewRecord
{
    protected static string $resource = MetroResource::class;

    public function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->form(Metro::getForm()),
        ];
    }
}
