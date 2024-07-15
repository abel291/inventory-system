<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $type = ExpenseType::factory(8)->create();
        Expense::factory(32)
            ->state(function (array $attributes) use ($type) {
                return [
                    'expense_type_id' => $type->random()->id
                ];
            })->create();
    }
}
