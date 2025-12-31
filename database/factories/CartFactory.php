<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CartStatus;
use App\Enums\CartType;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cart>
 */
class CartFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => CartType::Cart,
            'status' => CartStatus::Active,
            'name' => null,
            'expires_at' => now()->addMinutes(60),
        ];
    }

    public function wishlist(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CartType::Wishlist,
            'expires_at' => null,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CartStatus::Expired,
            'expires_at' => now()->subMinutes(10),
        ]);
    }

    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CartStatus::Sold,
        ]);
    }
}
