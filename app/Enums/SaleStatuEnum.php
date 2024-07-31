<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SaleStatuEnum: string implements HasLabel, HasColor, HasIcon
{

    case ACCEPTED = 'accepted';
    case CANCELLED = 'rejected';
    case REFUNDED = 'refunded';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::REFUNDED => 'Devolucion',
            self::ACCEPTED => 'Aceptada',
            self::CANCELLED => 'Cancelada',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ACCEPTED => 'success',
            self::REFUNDED => 'danger',
            self::CANCELLED => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::REFUNDED => 'heroicon-m-receipt-refund',
            self::ACCEPTED => 'heroicon-m-check-circle',
            self::CANCELLED => 'heroicon-m-x-circle',
        };
    }
}
