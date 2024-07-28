<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SalePaymentTypeEnum: string implements HasLabel, HasColor, HasIcon
{

    case CREDIT = 'credit';
    case CASH = 'cash';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::CREDIT => 'Credito',
            self::CASH => 'De contado',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CREDIT => 'info',
            self::CASH => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::CREDIT => 'heroicon-s-credit-card',
            self::CASH => 'heroicon-s-currency-dollar',
        };
    }
}
