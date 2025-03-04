<?php

namespace Fuelviews\SabHeroBlog\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;

enum MetroType: string implements HasColor, HasIcon
{
    case STATE = 'state';
    case CITY = 'city';

    public function getColor(): string
    {
        return match ($this) {
            self::STATE => 'info',
            self::CITY => 'cyan',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::STATE => 'heroicon-o-map-pin',
            self::CITY => 'heroicon-o-building-office-2',
        };
    }
}
