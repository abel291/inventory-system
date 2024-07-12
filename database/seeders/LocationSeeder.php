<?php

namespace Database\Seeders;

use App\Enums\LocationTypeEnum;
use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::truncate();
        Location::factory()->count(2)->create([
            'type' => LocationTypeEnum::STORE,
        ]);
        Location::factory()->count(2)->create([
            'type' => LocationTypeEnum::WAREHOUSE,
        ]);
    }
}
