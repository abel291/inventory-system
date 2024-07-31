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
use Illuminate\Support\Number;

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
        $users = User::select('id', 'name')->get();
        $products = Product::select('id', 'price')->inRandomOrder()->get();

        foreach ($products->chunk(20)->multiply(2) as $products_chunk) {
            foreach ($locations->random(3) as $location) {

                $stockEntry = StockEntry::factory()
                    ->create([
                        'location_id' => $location->id,
                        'user_id' => $users->random()->id,
                    ]);

                $productsEntry = [];

                foreach ($products_chunk as $product) {
                    $productsEntry[$product->id] = [
                        'quantity' => rand(1, 24),
                        'cost' => round($product->price * 0.70),
                    ];
                }

                $stockEntry->products()->attach($productsEntry);

                StockService::stockEntryAddition($stockEntry);

                $this->command->info("Entrada:{$location->name} - " . Number::currency(collect($productsEntry)->sum('cost')));
            }
        }
    }
}
