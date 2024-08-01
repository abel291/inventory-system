<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payroll>
 */
class PayrollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-12 month', 'now');
        return [
            'note' => fake()->sentence(),
            'amount' => rand(1, 1000) * 1000,
            'payment_at' => fake()->dateTimeBetween('-12 month', 'now'),
            'worker_id' => Worker::factory(),
            'user_id' => User::factory(),
        ];
    }
}
