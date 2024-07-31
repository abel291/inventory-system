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
    case SALE_REFUND  = 'sale_refund';
    case SALE_CANCELLED  = 'sale_cancelled';
    case RETURN  = 'return';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::ENTRY => 'Entrada',
            self::TRANSFER => 'Transferencia',
            self::ADJUSTMEN => 'Ajuste',
            self::SALE => 'Venta',
            self::SALE_REFUND => 'Venta devolucion',
            self::SALE_CANCELLED => 'Venta cancelada',
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
            self::SALE_REFUND => 'danger',
            self::SALE_CANCELLED => 'danger',
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
            self::SALE_REFUND => 'heroicon-m-receipt-refund',
            self::SALE_CANCELLED => 'heroicon-m-x-circle',
            self::RETURN => 'heroicon-m-arrow-down',
        };
    }
}
