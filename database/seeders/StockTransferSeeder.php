<?php

namespace Database\Seeders;

use App\Enums\StockAdjustmentTypeEnum;
use App\Models\Location;
use App\Models\Stock;
use App\Models\StockAdjustment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockTransferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = Location::get();
        StockAdjustment::where('type', StockAdjustmentTypeEnum::TRANSFER)->truncate();
        $users = User::get();
        foreach ($locations as $key => $location) {

            $locationTo = $locations->whereNot('id', $location->id)->random()->id;

            $stockTransfer = StockAdjustment::factory()
                ->recycle($users)
                ->create([
                    'type' => StockAdjustmentTypeEnum::TRANSFER,
                    'location_id' => $location->id,
                    'location_to_id' => $locationTo
                ]);

            $stock_selected = Stock::with('product', 'location')
                ->inRandomOrder()
                ->where('location_id', $location->id)
                ->take(5)
                ->get();

            $productsTranfers = [];
            foreach ($stock_selected as $item) {
                $productsTranfers[] = [
                    'quantity' => rand(1, $item->quantity),
                ];
            }

            $stockTransfer->products()->attach($productsTranfers);
        }
    }
}
