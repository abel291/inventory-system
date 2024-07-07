<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
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
        $stock = [];
        foreach (Product::select('id')->get() as $product) {
            foreach ($locations->random(2) as $location) {
                $stock[] = [
                    'product_id' => $product->id,
                    'location_id' => $location->id,
                    'stock' => rand(10, 40),
                    'security_stock' => rand(4, 10),
                ];
            }
        }

        foreach (array_chunk($stock, 800) as $data) {
            Stock::insert($data);
        }
    }
}
