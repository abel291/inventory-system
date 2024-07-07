<?php

namespace Database\Seeders;

use App\Enums\StockAdjustmentTypeEnum;
use App\Models\Stock;
use App\Models\StockAdjustment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockAdjustmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stock_products = [];
        $users = User::select('id')->get();
        foreach (Stock::select('id', 'stock')->get() as $stock) {

            $typeOperaction = fake()->randomElement(StockAdjustmentTypeEnum::cases());
            // $typeOperaction = fake()->randomElement(StockAdjustmentTypeEnum::cases());

            $adjustment = rand(1, $stock->stock);

            if ($typeOperaction ==  StockAdjustmentTypeEnum::INCREASE) {
                $final_stock = $adjustment + $stock->stock;
            } else {
                $final_stock = $stock->stock - $adjustment;
            }

            $stock_products[] = [
                'initial_stock' => $stock->stock,
                'adjustment' => $adjustment,
                'final_stock' => $final_stock,
                'type' => $typeOperaction,
                'note' => fake()->sentence(),
                'approved' => rand(1, 0),
                'user_id' => $users->random()->id,
                'stock_id' => $stock->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        shuffle($stock_products);


        foreach (array_chunk($stock_products, 400) as $items_slice) {
            StockAdjustment::insert($items_slice);
        }
    }
}
