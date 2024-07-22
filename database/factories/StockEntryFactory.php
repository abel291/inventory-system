<?php

namespace Database\Factories;

use App\Enums\StockStatuEnum;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockEntry>
 */
class StockEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(StockStatuEnum::cases());
        return [
            'status' => $status,
            'status_at' => $status != StockStatuEnum::PENDING ? now() : null,
            'note' => fake()->randomElement([null, fake()->sentence()]),
            'user_id' => User::factory(),
            'location_id' => Location::factory(),

        ];
    }
}
