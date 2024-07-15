<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reason' => fake()->sentence(),
            'amount' => rand(10, 100) * 1000,
            'note' => fake()->sentence(),
            'date' => fake()->dateTimeBetween('-30 days', '+30 days')
        ];
    }
}
