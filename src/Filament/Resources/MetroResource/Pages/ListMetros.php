<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\MetroResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Fuelviews\SabHeroBlog\Filament\Resources\MetroResource;

class ListMetros extends ListRecords
{
    protected static string $resource = MetroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
