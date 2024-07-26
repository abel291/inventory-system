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
            self::REFUNDED => 'warning',
            self::ACCEPTED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::REFUNDED => 'heroicon-s-receipt-refund',
            self::ACCEPTED => 'heroicon-s-check-circle',
            self::CANCELLED => 'heroicon-m-x-circle',
        };
    }
}
