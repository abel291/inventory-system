<?php

namespace Database\Seeders;

use App\Models\Payroll;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Worker::truncate();
        $responsibles = User::get();

        Worker::factory()->count(20)
            ->has(
                Payroll::factory()->recycle($responsibles)->count(rand(8, 14))
            )
            ->create();
    }
}
