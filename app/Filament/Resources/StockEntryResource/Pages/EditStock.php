<?php

namespace App\Filament\Resources\StockResource\Pages;

use App\Filament\Resources\StockResource;
use App\Models\Stock;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStock extends EditRecord
{
    protected static string $resource = StockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $stock = Stock::where([
            ['product_id', $data['product_id']],
            ['location_id', $data['location_id']],
            ['type', 'total']
        ])->first();

        $data['actual_stock'] = $stock ? $stock->remaining : 0;

        return $data;
    }
}
