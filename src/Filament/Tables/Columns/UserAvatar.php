<?php

namespace Fuelviews\SabHeroBlog\Filament\Tables\Columns;

use Filament\Tables\Columns\Column;

class UserAvatar extends Column
{
    protected string $view = 'sabhero-blog::filament.tables.columns.user-avatar';

    protected function getViewData(): array
    {
        return [
            'record' => $this->getRecord(),
        ];
    }
}
