<?php

namespace Database\Seeders;

use App\Enums\StockAdjustmentTypeEnum;
use App\Enums\StockStatuEnum;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockEntryProduct;
use App\Models\Stock;
use App\Models\StockAdjustment;
use App\Models\StockEntry;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Stock::truncate();
        StockEntry::truncate();
        StockMovement::truncate();
        DB::table('stock_entry_product')->truncate();

        $locations = Location::select('id')->get();
        $users = User::select('id')->get();
        $products = Product::select('id', 'price')->inRandomOrder()->get();

        foreach ($products->chunk(20)->multiply(3) as $products_chunk) {
            foreach ($locations->random(3) as $location) {

                $stockEntry = StockEntry::factory()
                    ->create([
                        'location_id' => $location->id,
                        'user_id' => $users->random()->id,
                    ]);

                $productsEntry = [];

                foreach ($products_chunk as $product) {
                    $quantity = fake()->randomElement([6, 12, 24]);
                    $productsEntry[$product->id] = [
                        'quantity' => $quantity,
                        'cost' => round($product->price * 0.70),
                    ];
                }

                $stockEntry->products()->attach($productsEntry);

                StockService::stockEntryAddition($stockEntry);

                $this->command->info($stockEntry->id);
            }
        }
    }
}
