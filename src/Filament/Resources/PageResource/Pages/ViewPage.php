<?php

namespace Fuelviews\SabHeroArticle\Filament\Resources\PageResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Fuelviews\SabHeroArticle\Filament\Resources\PageResource;
use Fuelviews\SabHeroArticle\Models\Page;

class ViewPage extends ViewRecord
{
    protected static string $resource = PageResource::class;

    public function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->form(Page::getForm()),
        ];
    }
}
