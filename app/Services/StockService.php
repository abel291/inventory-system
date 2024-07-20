<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockEntry;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\Artisan;

class StockService
{
    public static function stockEntryAddition(StockEntry $stockEntry)
    {
        foreach ($stockEntry->products as $product) {
            StockMovement::create([
                'location_id' => $stockEntry->location_id,
                'product_id' => $product->id,
                'type' => 'addition',//subtraction
                'movement_type' => 'entry',
                'quantity' => $product->pivot->quantity,
            ]);
        }
    }

    public static function stockTransfer(StockTransfer $stockTransfer)
    {
        foreach ($stockTransfer->products as $product) {

            StockMovement::create([
                'location_id' => $stockTransfer->location_from_id,
                'product_id' => $product->id,
                'type' => 'subtraction',
                'movement_type' => 'transfer',
                'quantity' => $product->pivot->quantity,
            ]);
            StockMovement::create([
                'location_id' => $stockTransfer->location_to_id,
                'product_id' => $product->id,
                'type' => 'addition',
                'movement_type' => 'transfer',
                'quantity' => $product->pivot->quantity,
            ]);

        }
    }
}
