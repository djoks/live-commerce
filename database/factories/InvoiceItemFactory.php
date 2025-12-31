<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::factory()->create();

        return [
            'invoice_id' => Invoice::factory(),
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => fake()->numberBetween(1, 5),
            'unit_price' => $product->price,
        ];
    }
}
