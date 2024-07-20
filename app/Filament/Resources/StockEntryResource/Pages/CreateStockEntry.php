<?php

namespace App\Filament\Resources\StockEntryResource\Pages;

use App\Filament\Resources\StockEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStockEntry extends CreateRecord
{
    protected static string $resource = StockEntryResource::class;
    public static ?string $pluralModelLabel = 'Entrada mercancia';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
