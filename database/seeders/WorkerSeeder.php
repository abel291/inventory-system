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
        Payroll::truncate();

        $responsibles = User::get();

        Worker::factory()->count(10)
            ->has(
                Payroll::factory()->recycle($responsibles)->count(rand(2, 3))
            )
            ->create();
    }
}
