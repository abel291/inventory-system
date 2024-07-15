<?php

namespace Database\Seeders;

use App\Enums\StockAdjustmentTypeEnum;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockEntryProduct;
use App\Models\Stock;
use App\Models\StockAdjustment;
use App\Models\StockEntry;
use App\Models\User;
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
        StockAdjustment::truncate();
        DB::table('stock_adjustment_product')->truncate();

        $locations = Location::select('id')->get();
        $users = User::select('id')->get();
        $products = Product::select('id', 'price')->inRandomOrder()->get();
        foreach ($products->multiply(4)->chunk(12) as $products_chunk) {
            $stockEntry = StockAdjustment::factory()
                ->recycle($locations)
                ->recycle($users)
                ->create([
                    'type' => StockAdjustmentTypeEnum::ENTRY
                ]);

            $productsEntry = [];
            foreach ($products_chunk as $product) {
                $quantity = rand(1, 4);
                $productsEntry[$product->id] = [
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'cost' => round($product->price * 0.70),
                ];
            }
            $stockEntry->products()->attach($productsEntry);
        }

        $totalStock = [];
        foreach (StockAdjustment::with('products')->get() as $stockAdjustment) {
            foreach ($stockAdjustment->products as $key => $product) {
                $totalStock[] = [
                    'product_id' => $product->id,
                    'location_id' => $stockAdjustment->location_id,
                    'quantity' => $product->stock->quantity,
                    'price' => $product->stock->price,
                ];
            }
        }

        $totalStock = collect($totalStock)->groupBy(function (array $item) {
            return $item['product_id'] . $item['location_id'];
        })->map(function ($item) {

            return [
                ...$item[0],
                'quantity' => $item->sum('quantity'),
            ];
        })->values()->toArray();

        foreach (array_chunk($totalStock, 1000) as $stock) {
            Stock::insert($stock);
        }
    }
}
