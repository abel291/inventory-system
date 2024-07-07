<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StockAdjustmentTypeEnum: string implements HasLabel, HasColor, HasIcon
{
    case INCREASE = 'increase';
    case DECREASE = 'decrease';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::INCREASE => 'Incremento',
            self::DECREASE => 'Disminucion',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::INCREASE => 'success',
            self::DECREASE => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::INCREASE => 'heroicon-m-arrow-up',
            self::DECREASE => 'heroicon-m-arrow-down',
        };
    }
}
