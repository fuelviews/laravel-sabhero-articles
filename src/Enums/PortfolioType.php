<?php

namespace Fuelviews\SabHeroBlog\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PortfolioType: string implements HasColor, HasLabel
{
    case ALL = 'all';
    case RESIDENTIAL = 'residential';
    case COMMERCIAL = 'commercial';

    public function getColor(): string
    {
        return match ($this) {
            self::ALL => 'gray',
            self::RESIDENTIAL => 'success',
            self::COMMERCIAL => 'info',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ALL => 'All',
            self::RESIDENTIAL => 'Residential',
            self::COMMERCIAL => 'Commercial',
        };
    }
}