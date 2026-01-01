<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\CartType;
use App\Models\Cart;
use App\Models\CartItem;

/**
 * Contract for cart repository operations.
 *
 * Handles shopping cart and wishlist persistence and lifecycle management.
 */
interface CartContract
{
    /**
     * Find an active cart for a user by type.
     */
    public function findByUserId(int $userId, CartType $type = CartType::Cart): ?Cart;

    /**
     * Create a new cart for a user.
     */
    public function create(int $userId, CartType $type = CartType::Cart): Cart;

    /**
     * Mark a cart as expired (no longer active).
     */
    public function markAsExpired(Cart $cart): void;

    /**
     * Mark a cart as sold (checkout completed).
     */
    public function markAsSold(Cart $cart): void;

    /**
     * Find a cart item by cart ID and product ID.
     */
    public function findItem(int $cartId, int $productId): ?CartItem;

    /**
     * Add an item to a cart, or increment quantity if it already exists.
     */
    public function addItem(int $cartId, int $productId, int $quantity = 1): CartItem;

    /**
     * Find or create a cart item with quantity of 1.
     */
    public function findOrCreateItem(int $cartId, int $productId): CartItem;

    /**
     * Update a cart item's quantity.
     */
    public function updateItemQuantity(CartItem $item, int $quantity): CartItem;

    /**
     * Remove an item from a cart.
     */
    public function removeItem(int $cartId, int $productId): void;
}
