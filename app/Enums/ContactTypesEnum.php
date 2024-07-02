<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ContactTypesEnum: string implements HasLabel, HasColor
{
    case CLIENT = 'client';
    case PROVIDER = 'provider';
    case CLIENT_PROVIDER = 'client-provider';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CLIENT => 'Cliente',
            self::PROVIDER => 'Proveedor',
            self::CLIENT_PROVIDER => 'Cliente y Proveedor',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::CLIENT => 'success',
            self::PROVIDER => 'warning',
            self::CLIENT_PROVIDER => 'gray',
        };
    }
}
