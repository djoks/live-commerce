<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\CartType;
use App\Models\Cart;

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
}
