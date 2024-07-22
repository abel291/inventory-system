<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StockMovementTypeEnum: string implements HasLabel, HasColor, HasIcon
{
    case ENTRY = 'entry';
    case TRANSFER  = 'transfer';
    case ADJUSTMEN  = 'adjustmen';
    case SALE  = 'sale';
    case RETURN  = 'return';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::ENTRY => 'Entrada',
            self::TRANSFER => 'Transferencia',
            self::ADJUSTMEN => 'Ajuste',
            self::SALE => 'Venta',
            self::RETURN => 'Devolucion',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::ENTRY => 'success',
            self::TRANSFER => 'info',
            self::ADJUSTMEN => 'gray',
            self::SALE => 'success',
            self::RETURN => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::ENTRY => 'heroicon-m-arrow-up',
            self::TRANSFER => 'heroicon-m-arrow-down',
            self::ADJUSTMEN => 'heroicon-m-arrow-down',
            self::SALE => 'heroicon-m-arrow-down',
            self::RETURN => 'heroicon-m-arrow-down',
        };
    }
}
