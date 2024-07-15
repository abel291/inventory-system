<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockEntryProduct>
 */
class StockEntryProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = rand(40, 220) * 1000;
        return [
            'quantity' => rand(6, 12),
            'price' => $price,
            'cost' => round($price / 0.70),
            'product_id' => Product::factory(),
        ];
    }
}
