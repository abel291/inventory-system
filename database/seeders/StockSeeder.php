<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Stock::truncate();
        $locations = Location::select('id')->get();
        $users = User::select('id')->get();
        $stock = [];

        foreach (Product::select('id', 'price')->get() as $product) {
            foreach ($locations->random(3)->multiply(rand(2, 4)) as $location) {
                $quantity = rand(1, 4);
                $stock[] = [
                    'product_id' => $product->id,
                    'location_id' => $location->id,
                    'quantity' => $quantity,
                    'remaining' => $quantity,
                    'cost' => round($product->price * 0.80),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'type' => 'stock',
                    'user_id' => $users->random()->id
                ];
            }
        }

        // shuffle($stock);
        // $stockTotal = collect($stock)->groupBy(function (array $item, int $key) {
        //     return $item['product_id'] . $item['location_id'];
        // })->map(function ($item) {

        //     return [
        //         ...$item[0],
        //         'quantity' => $item->sum('quantity'),
        //         'remaining' => $item->sum('remaining'),
        //         'type' => 'total',
        //         'user_id' => null,
        //     ];
        // })->toArray();

        foreach ($stock as $data) {
            Stock::create($data);
        }

        // foreach (array_chunk(array_merge($stock, $stockTotal), 800) as $data) {
        //     Stock::insert($data);
        // }
    }
}
