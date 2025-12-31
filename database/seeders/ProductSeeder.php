<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        foreach ($categories as $category) {
            // Only create products if none exist to avoid duplication on repeated runs
            if ($category->products()->count() === 0) {
                Product::factory(10)
                    ->configure()
                    ->create(['category_id' => $category->id]);
            }
        }
    }
}
