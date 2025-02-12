<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\AuthorResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Fuelviews\SabHeroBlog\Filament\Resources\AuthorResource;

class ListUsers extends ListRecords
{
    protected static string $resource = AuthorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
