<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StockStatuEnum: string implements HasLabel, HasColor, HasIcon
{
    case PENDING = 'pending';
    case ACCEPTED  = 'accepted';
    case REJECTED  = 'rejected';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::ACCEPTED => 'Aceptada',
            self::REJECTED => 'Rechazada',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::ACCEPTED => 'success',
            self::REJECTED => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-m-arrow-path',
            self::ACCEPTED => 'heroicon-m-check',
            self::REJECTED => 'heroicon-m-x-mark',
        };
    }
}
