<?php

namespace Fuelviews\SabHeroBlog\Filament\Tables\Columns;

use Filament\Tables\Columns\Column;

class UserPhotoName extends Column
{
    protected string $view = 'sabhero-blog::filament.tables.columns.user-photo-name';

    protected function getViewData(): array
    {
        return [
            'record' => $this->getRecord() ?? null,
        ];
    }
}
