<?php

namespace App\Filament\Resources\StockResource\Pages;

use App\Filament\Resources\StockResource;
use App\Models\Stock;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateStock extends CreateRecord
{
    protected static string $resource = StockResource::class;

    public static ?string $title   = 'Agregar mercancia';
    protected function handleRecordCreation(array $data): Model
    {

        dd($data);
        $stock = Stock::where('product_id', $data['product_id'])
            ->where('location_id', $data['location_id'])->first();
        if ($stock) {
            $stock->increment('stock', $data['stock']);
            return $stock;
        } else {
            return static::getModel()::create($data);
        }
    }
}
