<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StockAdjustmentTypeEnum: string implements HasLabel, HasColor, HasIcon
{
    case ENTRY = 'entry';
    case TRANSFER  = 'transfer';
    case NEGATIVE_TRANSFER  = 'negative_adjustment';
    case POSITIVE_TRANSFER  = 'positive_adjustment';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::ENTRY => 'Incremento',
            self::TRANSFER => 'Disminucion',
            self::NEGATIVE_TRANSFER => 'Disminucion',
            self::POSITIVE_TRANSFER => 'Disminucion',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::ENTRY => 'success',
            self::TRANSFER => 'danger',
            self::NEGATIVE_TRANSFER => 'danger',
            self::POSITIVE_TRANSFER => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::ENTRY => 'heroicon-m-arrow-up',
            self::TRANSFER => 'heroicon-m-arrow-down',
            self::NEGATIVE_TRANSFER => 'heroicon-m-arrow-down',
            self::POSITIVE_TRANSFER => 'heroicon-m-arrow-down',
        };
    }
}
