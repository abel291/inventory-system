<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LocationTypeEnum: string implements HasLabel, HasColor
{
    case WAREHOUSE = 'warehouse';
    case STORE = 'store';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::WAREHOUSE => 'Bodega',
            self::STORE => 'Tieda',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::WAREHOUSE => 'success',
            self::STORE => 'warning',
        };
    }
}
