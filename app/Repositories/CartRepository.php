<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\CartStatus;
use App\Enums\CartType;
use App\Models\Cart;
use App\Repositories\Contracts\CartContract;

/**
 * Eloquent implementation of the cart repository.
 */
class CartRepository implements CartContract
{
    /**
     * {@inheritDoc}
     */
    public function findByUserId(int $userId, CartType $type = CartType::Cart): ?Cart
    {
        return Cart::query()
            ->where('user_id', $userId)
            ->ofType($type)
            ->active()
            ->latest()
            ->first();
    }

    /**
     * {@inheritDoc}
     *
     * Shopping carts get an expiration time from config; wishlists don't expire.
     */
    public function create(int $userId, CartType $type = CartType::Cart): Cart
    {
        $expiresAt = $type === CartType::Cart
            ? now()->addMinutes((int) config('cart.ttl_minutes', 60))
            : null;

        return Cart::query()->create([
            'user_id' => $userId,
            'type' => $type,
            'status' => CartStatus::Active,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function markAsExpired(Cart $cart): void
    {
        $cart->update(['status' => CartStatus::Expired]);
    }

    /**
     * {@inheritDoc}
     */
    public function markAsSold(Cart $cart): void
    {
        $cart->update(['status' => CartStatus::Sold]);
    }
}
