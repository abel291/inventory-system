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

        $products_json = collect(Storage::json(env('DB_FAKE_PRODUCTS')))->shuffle();

        $categories = Category::select('id', 'name')->get()->pluck('id', 'name');


        foreach ($products_json as $key => $product) {

            $price = $product['price'];

            $products_db[] = Product::factory()->make([
                'name' => $product['name'],
                'img' => $product['img'],
                'price' => $price,
                'created_at' => now(),
                'updated_at' => now(),
                'category_id' => $categories[$product['category']],
            ])->toArray();
        }


        foreach (array_chunk($products_db, 800) as $products_slice_array) {
            Product::insert($products_slice_array);
        }
    }
}
