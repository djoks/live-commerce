<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\CartStatus;
use App\Enums\CartType;
use App\Models\Cart;
use App\Models\CartItem;
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

    /**
     * {@inheritDoc}
     */
    public function findItem(int $cartId, int $productId): ?CartItem
    {
        return CartItem::query()
            ->where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->first();
    }

    /**
     * {@inheritDoc}
     */
    public function addItem(int $cartId, int $productId, int $quantity = 1): CartItem
    {
        $item = $this->findItem($cartId, $productId);

        if ($item) {
            $item->increment('quantity', $quantity);

            /** @var CartItem $fresh */
            $fresh = $item->fresh(['product']);

            return $fresh;
        }

        return CartItem::query()->create([
            'cart_id' => $cartId,
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function findOrCreateItem(int $cartId, int $productId): CartItem
    {
        return CartItem::query()->firstOrCreate(
            ['cart_id' => $cartId, 'product_id' => $productId],
            ['quantity' => 1]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function updateItemQuantity(CartItem $item, int $quantity): CartItem
    {
        $item->update(['quantity' => $quantity]);

        /** @var CartItem $fresh */
        $fresh = $item->fresh(['product']);

        return $fresh;
    }

    /**
     * {@inheritDoc}
     */
    public function removeItem(int $cartId, int $productId): void
    {
        CartItem::query()
            ->where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->delete();
    }
}
