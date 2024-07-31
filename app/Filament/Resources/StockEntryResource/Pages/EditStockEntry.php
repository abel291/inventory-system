<?php

namespace App\Filament\Resources\StockEntryResource\Pages;

use App\Filament\Resources\StockEntryResource;
use App\Filament\Resources\StockResource;
use App\Models\Stock;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockEntry extends EditRecord
{
    protected static string $resource = StockEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // protected function mutateFormDataBeforeFill(array $data): array
    // {
    //     $stock = Stock::where([
    //         ['product_id', $data['product_id']],
    //         ['location_id', $data['location_id']],
    //         ['type', 'total']
    //     ])->first();

    //     $data['actual_stock'] = $stock ? $stock->remaining : 0;

    //     return $data;
    // }
}
