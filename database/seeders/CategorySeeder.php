<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::truncate();
        // dd(env('DB_FAKE_PRODUCTS'));
        $products = collect(Storage::json(env('DB_FAKE_PRODUCTS')));

        $categories = $products->unique('category')->map(function ($item) {

            $slug = Str::slug($item['category']);
            return Category::factory()->make([
                'name' => $item['category'],
                'slug' => $slug,
                'img' => "img/categories/$slug.png",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
        Category::insert($categories->toArray());
    }
}
