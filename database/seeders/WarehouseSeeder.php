<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouse::truncate();

        $products = Product::select('id')->get();
        $product_warehouse = [];
        $warehouses = Warehouse::factory()->count(10)->create();

        foreach ($warehouses as $warehouse) {
            foreach ($products->random(rand(100, 200)) as $product) {
                $product_warehouse[] = [
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach (array_chunk($product_warehouse, 800) as $data) {
            DB::table('product_warehouse')->insert($data);
        }




        // $warehouses = [
        //     'House',
        //     'Avanzadas',
        //     'Bodeguita',
        //     'Seguridad Bodegas',
        //     'Casa de Acero',
        //     'Comerciales',
        //     'Plus',
        //     'Individuales',
        // ];

        // foreach ($warehouses as $key => $warehouse) {
        //     $data[] = Warehouse::facotry()->make([
        //         'name' => $warehouse,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);
        // }
        // Warehouse::insert($data);
    }
}
