<?php

namespace App\Observers;

use App\Models\Stock;
use App\Models\StockAdjustment;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class StockAdjustmentObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Stock "created" event.
     */
    public function created(StockAdjustment $stockAdjustment): void
    {

        $this->calculatedStockRemaining($stockAdjustment);
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

    public function calculatedStockRemaining(StockAdjustment $stockAdjustment): void
    {

        foreach (stockAdjustment::with('products')->get() as $stockAdjustment) {
            foreach ($stockAdjustment->products as $product) {

                Stock::where([
                    ['product_id', $product->id],
                    ['location_id', $stockAdjustment->location_id],
                ])->increment('quantity', $product->stock->quantity);

                Stock::where([
                    ['product_id', $product->id],
                    ['location_id', $stockAdjustment->location__to_id],
                ])->decrement('quantity', $product->stock->quantity);
            }
        }

        // if ($stock->type == 'stock') {
        //     $newStock = Stock::where([
        //         ['product_id', $stock->product_id],
        //         ['location_id', $stock->location_id],
        //     ])->orderBy('id', 'desc')->get();

        //     Stock::updateOrCreate(
        //         [
        //             'product_id' => $stock->product_id,
        //             'location_id' => $stock->location_id,
        //             'type' => 'total'
        //         ],
        //         [
        //             'quantity' => $newStock->sum('quantity'),
        //             'remaining' => $newStock->sum('remaining'),
        //             'cost' => $newStock->isNotEmpty() ? $newStock->last()->cost : $stock->cost

        //         ]
        //     );
        // }
    }
}
