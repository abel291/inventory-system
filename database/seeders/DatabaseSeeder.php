<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Expense;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            WorkerSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            LocationSeeder::class,
            ContactSeeder::class,
            ExpenseSeeder::class,
            PaymentMethodSeeder::class,
            StockEntrySeeder::class,
            StockTransferSeeder::class,
            SaleSeeder::class,

        ]);
    }
}
