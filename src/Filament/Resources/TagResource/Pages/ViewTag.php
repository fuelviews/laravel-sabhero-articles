<?php

namespace Fuelviews\SabHeroArticles\Filament\Resources\TagResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Fuelviews\SabHeroArticles\Filament\Resources\TagResource;
use Fuelviews\SabHeroArticles\Models\Tag;

class ViewTag extends ViewRecord
{
    protected static string $resource = TagResource::class;

    public function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->form(Tag::getForm()),
        ];
    }
}
