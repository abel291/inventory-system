<?php

namespace App\Observers;

use App\Models\Stock;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class StockObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Stock "created" event.
     */
    public function created(Stock $stock): void
    {

        $this->calculatedStockRemaining($stock);
    }

    /**
     * Handle the Stock "updated" event.
     */
    public function updated(Stock $stock): void
    {
        //
    }

    /**
     * Handle the Stock "deleted" event.
     */
    public function deleted(Stock $stock): void
    {
        $this->calculatedStockRemaining($stock);
    }

    /**
     * Handle the Stock "restored" event.
     */
    public function restored(Stock $stock): void
    {
        //
    }

    /**
     * Handle the Stock "force deleted" event.
     */
    public function forceDeleted(Stock $stock): void
    {
        //
    }

    public function calculatedStockRemaining(Stock $stock): void
    {
        if ($stock->type == 'stock') {
            $newStock = Stock::where([
                ['product_id', $stock->product_id],
                ['location_id', $stock->location_id],
                ['remaining', '>', 0],
                ['type', 'stock'],
            ])->orderBy('id', 'desc')->get();


            // if ($totalStock) {
            //     $totalStock->quantity = $newStock->sum('quantity');
            //     $totalStock->remaining = $newStock->sum('remaining');
            //     $totalStock-> = $newStock->cost;
            //     $totalStock->save();
            // }
            Stock::updateOrCreate(
                [
                    'product_id' => $stock->product_id,
                    'location_id' => $stock->location_id,
                    'type' => 'total'
                ],
                [
                    'quantity' => $newStock->sum('quantity'),
                    'remaining' => $newStock->sum('remaining'),
                    'cost' => $newStock->isNotEmpty() ? $newStock->last()->cost : $stock->cost

                ]
            );
        }
    }
}
