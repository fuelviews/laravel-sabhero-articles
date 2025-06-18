<?php

namespace Fuelviews\SabHeroArticle\Filament\Resources\TagResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Fuelviews\SabHeroArticle\Filament\Resources\TagResource;
use Fuelviews\SabHeroArticle\Models\Tag;

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
