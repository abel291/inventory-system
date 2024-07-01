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
        $name = $this->faker->words(7, true);

        $price = rand(100, 1000); //$100 - $1.000



        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description_min' => $this->faker->text(250),
            'price' => $price,
            'dicount' => $this->faker->randomElement([0, 10, 20, 30, 40, 50]),
            'cost' => round($price * 0.80),
            'img' => 'item-' . rand(1, 52) . '.jpg',
            'min_quantity' => rand(1, 10),
            'max_quantity' => rand(10, 40),
            'active' => 1,
        ];
    }
}
