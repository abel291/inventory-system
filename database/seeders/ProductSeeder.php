<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::truncate();

        $products_json = collect(Storage::json("products/products.json"))->shuffle()->take(10);

        $faker = Faker\Factory::create();

        $categories = Category::select('id', 'name')->get()->pluck('id', 'name');


        foreach ($products_json as $key => $product) {

            // $this->command->line($key);

            $price = $product['price'];

            // $discount = rand(0, 1) ? $faker->randomElement([10, 20, 30, 40, 50]) : 0;

            // $price_discount = $price - ($price * ($discount / 100));

            $cost = round($price * 0.80);

            $products_db[] = Product::factory()->make([
                'name' => $product['name'],
                // 'slug' => Str::slug($product['name'], '-') . rand(0, 99999),
                'img' => $product['img'],
                'cost' => $cost,
                'price' => $price,
                // 'discount' => $discount,
                // 'price_discount' => $price_discount,
                'created_at' => now(),
                'updated_at' => now(),
                'category_id' => $categories[$product['category']],

            ])->toArray();
        }


        foreach (array_chunk($products_db, 400) as $products_slice_array) {
            Product::insert($products_slice_array);
        }
    }
}
