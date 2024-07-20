<?php

namespace Database\Seeders;

use App\Enums\StockAdjustmentTypeEnum;
use App\Models\Location;
use App\Models\Stock;
use App\Models\StockAdjustment;
use App\Models\StockTransfer;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockTransferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StockTransfer::truncate();
        DB::table("stock_transfer_product")->truncate();

        $locations = Location::get();
        $users = User::get();
        foreach ($locations->multiply(10) as $locationFrom) {

            $locationTo = $locations->where('id', '!=', $locationFrom->id)->random();

            $stockTransfer = StockTransfer::factory()
                ->recycle($users)
                ->create([
                    'location_from_id' => $locationFrom->id,
                    'location_to_id' => $locationTo->id
                ]);

            $stock_selected = Stock::with('product')
                ->inRandomOrder()
                ->where('location_id', $locationFrom->id)
                ->where('quantity', '>', 0)
                ->take(rand(6, 12))
                ->get();

            $productsTranfers = [];
            foreach ($stock_selected as $item) {
                $productsTranfers[$item->product_id] = [
                    'quantity' => rand(1, $item->quantity),
                ];
            }


            $stockTransfer->products()->attach($productsTranfers);

            StockService::stockTransfer($stockTransfer);

            $this->command->info($stockTransfer->id);
        }
    }
}
