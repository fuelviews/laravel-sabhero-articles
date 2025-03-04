<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\TagResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Fuelviews\SabHeroBlog\Filament\Resources\TagResource;

class ListTags extends ListRecords
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
