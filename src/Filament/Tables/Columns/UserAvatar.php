<?php

namespace Fuelviews\SabHeroArticle\Filament\Tables\Columns;

use Filament\Tables\Columns\Column;

class UserAvatar extends Column
{
    protected string $view = 'sabhero-article::filament.tables.columns.user-avatar';

    public function getViewData(): array
    {
        return [
            'record' => $this->getRecord(),
        ];
    }
}
