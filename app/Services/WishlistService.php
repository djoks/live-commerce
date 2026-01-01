<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CartType;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Repositories\Contracts\CartContract;

/**
 * Service class for managing user wishlists.
 *
 * Wishlists are implemented as a special type of cart that doesn't expire.
 * Users can save products for later and move them to their shopping cart.
 */
class WishlistService
{
    public function __construct(
        private CartContract $cartRepository,
        private CartService $cartService
    ) {}

    /**
     * Get the user's default wishlist, creating one if it doesn't exist.
     */
    public function getOrCreateDefaultWishlist(User $user): Cart
    {
        $wishlist = $this->cartRepository->findByUserId($user->id, CartType::Wishlist);

        if (! $wishlist) {
            $wishlist = $this->cartRepository->create($user->id, CartType::Wishlist);
        }

        return $wishlist->load('items.product');
    }

    /**
     * Add a product to the user's wishlist.
     *
     * Wishlist items always have quantity of 1 (just tracking interest).
     */
    public function addItem(User $user, int $productId): CartItem
    {
        $wishlist = $this->getOrCreateDefaultWishlist($user);

        return $this->cartRepository->findOrCreateItem($wishlist->id, $productId);
    }

    /**
     * Remove a product from the user's wishlist.
     */
    public function removeItem(User $user, int $productId): void
    {
        $wishlist = $this->getOrCreateDefaultWishlist($user);

        $this->cartRepository->removeItem($wishlist->id, $productId);
    }

    /**
     * Move a product from wishlist to shopping cart.
     *
     * Removes from wishlist and adds to cart in one operation.
     */
    public function moveToCart(User $user, int $productId): void
    {
        $this->removeItem($user, $productId);
        $this->cartService->addItem($user, $productId, 1);
    }
}
