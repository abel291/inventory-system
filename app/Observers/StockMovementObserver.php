<?php

namespace App\Observers;

use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Artisan;

class StockMovementObserver
{
    /**
     * Handle the StockMovement "created" event.
     */
    public function created(StockMovement $stockMovement): void
    {

        $stock = Stock::firstOrNew([
            'location_id' => $stockMovement->location_id,
            'product_id' => $stockMovement->product_id,
        ]);

        match ($stockMovement->type) {
            'addition' => $stock->quantity += $stockMovement->quantity,
            'subtraction' => $stock->quantity -= $stockMovement->quantity,
        };
        $stock->save();
        // $stock->quantity = $stock->quantity + $stockMovement->quantity
    }

    /**
     * Handle the StockMovement "updated" event.
     */
    public function updated(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "deleted" event.
     */
    public function deleted(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "restored" event.
     */
    public function restored(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "force deleted" event.
     */
    public function forceDeleted(StockMovement $stockMovement): void
    {
        //
    }
}
