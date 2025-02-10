<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\MetroResource\Pages;

use Fuelviews\SabHeroBlog\Filament\Resources\MetroResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMetro extends ListRecords
{
    protected static string $resource = MetroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
