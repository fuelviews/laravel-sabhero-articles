<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\CategoryResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Fuelviews\SabHeroBlog\Filament\Resources\CategoryResource;
use Fuelviews\SabHeroBlog\Models\Category;

class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;

    public function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->form(Category::getForm()),
        ];
    }
}
