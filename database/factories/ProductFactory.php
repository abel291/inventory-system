<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(7, true);

        $price = rand(100, 1000); //$100 - $1.000

        $discount = fake()->randomElement([0, 10, 20, 30, 40, 50]);

        $price_discount = $price - ($price * ($discount / 100));

        $cost = round($price * 0.80);

        return [
            'name' => ucfirst($name),
            'barcode' => fake()->randomNumber(8, true),
            // 'slug' => Str::slug($name),
            'description_min' => fake()->text(250),
            'price' => $price,
            // 'discount' => $discount,
            // 'price_discount' => $price_discount,
            'cost' => $cost,
            // 'stock' => rand(10, 40),
            'img' => 'item-' . rand(1, 52) . '.jpg',
            'min_quantity' => rand(1, 10),
            'max_quantity' => rand(10, 40),
            'active' => 1,
        ];
    }
}
