<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subTotal = fake()->randomFloat(2, 50, 500);
        $tax = round($subTotal * 0.1, 2);

        return [
            'user_id' => User::factory(),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'delivery_address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country' => fake()->country(),
            'phone' => fake()->phoneNumber(),
            'sub_total' => $subTotal,
            'discount' => null,
            'tax' => $tax,
            'total_amount' => $subTotal + $tax,
            'status' => InvoiceStatus::Pending,
            'order_no' => strtoupper(fake()->unique()->bothify('??#??#??')), // Simple randomization for factory
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::Paid,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::Cancelled,
        ]);
    }
}
