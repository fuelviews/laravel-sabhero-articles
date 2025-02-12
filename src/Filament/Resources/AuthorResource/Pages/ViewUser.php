<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\AuthorResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Fuelviews\SabHeroBlog\Filament\Resources\AuthorResource;

class ViewUser extends ViewRecord
{
    protected static string $resource = AuthorResource::class;

    public function getHeaderActions(): array
    {
        return [
            EditAction::make()
        ];
    }
}
