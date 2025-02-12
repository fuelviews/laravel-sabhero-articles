<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\MetroResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Fuelviews\SabHeroBlog\Filament\Resources\MetroResource;

class EditMetro extends EditRecord
{
    protected static string $resource = MetroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
