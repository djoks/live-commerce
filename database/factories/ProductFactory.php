<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 500),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'status' => ProductStatus::Active,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            static $files = null;

            if ($files === null) {
                $files = glob(storage_path('app/public/uploads/*'));

                // Filter out non-image files if necessary, or just rely on Medialibrary handling it.
                // Given the user said "images or furniture", likely all files are valid directly or generally.
                // We'll shuffle to ensure random picking.
                if (is_array($files)) {
                    shuffle($files);
                }
            }

            // If we've exhausted the list, reload and reshuffle
            if (empty($files)) {
                $files = glob(storage_path('app/public/uploads/*'));
                if (is_array($files)) {
                    shuffle($files);
                }
            }

            if (! empty($files)) {
                $file = array_pop($files);

                // Ensure file exists and is readable
                if ($file && file_exists($file)) {
                    try {
                        $product->addMedia($file)
                            ->preservingOriginal()
                            ->toMediaCollection('images');
                    } catch (\Throwable $e) {
                        // Silently fail if image cannot be added, to avoid breaking the factory for all items
                    }
                }
            }
        });
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProductStatus::Draft,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => fake()->numberBetween(1, 5),
        ]);
    }
}
