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
        ExpenseType::truncate();
        Expense::truncate();

        $expensesTypeArray = [
            'Alquiler del espacio de trabajo',
            'Gastos de suministros',
            'Gastos de papelería y oficina',
            'Mobiliario y maquinaria',
            'Gastos de marketing y publicidad',
            'Gastos de servicios profesionales independientes',
            'Gastos generales de administración y dirección',
            'Gastos de diseño web y software',
            'Gastos de representación',
            'Gastos de ocio, entretenimiento y bienestar',
        ];


        foreach ($expensesTypeArray as $value) {
            ExpenseType::create(['name' => $value]);
        }

        $expensesType = ExpenseType::get();

        Expense::factory(32)
            ->state(function (array $attributes) use ($expensesType) {
                return [
                    'expense_type_id' => $expensesType->random()->id
                ];
            })->create();
    }
}
