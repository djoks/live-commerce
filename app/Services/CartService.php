<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CartType;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Repositories\Contracts\CartContract;
use Illuminate\Support\Facades\DB;

/**
 * Service class for managing shopping cart operations.
 *
 * Handles cart creation, item management, expiration, and wishlist transitions.
 */
class CartService
{
    public function __construct(
        private CartContract $cartRepository,
        private WishlistService $wishlistService
    ) {}

    /**
     * Retrieve the user's active cart or create a new one if none exists.
     *
     * Checks for an existing active cart. If expired, it's processed and a new one is created.
     *
     * @param  User  $user  The authenticated user.
     * @return Cart The active shopping cart.
     */
    public function getOrCreateActiveCart(User $user): Cart
    {
        $cart = $this->cartRepository->findByUserId($user->id, CartType::Cart);

        if ($cart) {
            $expiresAt = $cart->expires_at;
            if ($expiresAt !== null && $expiresAt->isPast()) {
                $this->expireCart($cart);
                $cart = null;
            }
        }

        if (! $cart) {
            $cart = $this->cartRepository->create($user->id, CartType::Cart);
        }

        return $cart->load('items.product');
    }

    /**
     * Add a product to the user's cart.
     *
     * Increments quantity if the item already exists using database-level operations.
     *
     * @param  User  $user  The authenticated user.
     * @param  int  $productId  The ID of the product to add.
     * @param  int  $quantity  The quantity to add (default: 1).
     * @return CartItem The created or updated cart item.
     */
    public function addItem(User $user, int $productId, int $quantity = 1): CartItem
    {
        $cart = $this->getOrCreateActiveCart($user);

        $cartItem = CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        $this->refreshExpiration($cart);

        /** @var CartItem $freshItem */
        $freshItem = $cartItem->fresh(['product']);

        return $freshItem;
    }

    /**
     * Update the quantity of a specific product in the user's cart.
     *
     * If quantity is <= 0, the item is removed.
     *
     * @param  User  $user  The authenticated user.
     * @param  int  $productId  The ID of the product.
     * @param  int  $quantity  The new quantity.
     * @return CartItem|null The updated item, or null if removed/not found.
     */
    public function updateItemQuantity(User $user, int $productId, int $quantity): ?CartItem
    {
        $cart = $this->getOrCreateActiveCart($user);

        $cartItem = CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();

        if (! $cartItem) {
            return null;
        }

        if ($quantity <= 0) {
            $cartItem->delete();

            return null;
        }

        $cartItem->update(['quantity' => $quantity]);
        $this->refreshExpiration($cart);

        return $cartItem->fresh(['product']);
    }

    /**
     * Remove a product from the user's cart.
     *
     * @param  User  $user  The authenticated user.
     * @param  int  $productId  The ID of the product to remove.
     */
    public function removeItem(User $user, int $productId): void
    {
        $cart = $this->getOrCreateActiveCart($user);

        CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->delete();

        $this->refreshExpiration($cart);
    }

    /**
     * Refresh the expiration timestamp of the cart.
     *
     * Extends the cart's lifespan by the configured TTL.
     *
     * @param  Cart  $cart  The cart to refresh.
     */
    public function refreshExpiration(Cart $cart): void
    {
        $ttlMinutes = (int) config('cart.ttl_minutes', 60);
        $cart->update(['expires_at' => now()->addMinutes($ttlMinutes)]);
    }

    /**
     * Process an expired cart.
     *
     * Moves items to the user's wishlist and marks the cart as expired.
     *
     * @param  Cart  $cart  The expired cart.
     */
    public function expireCart(Cart $cart): void
    {
        DB::transaction(function () use ($cart) {
            $cart->loadMissing('user', 'items');
            $user = $cart->user;

            if ($user) {
                foreach ($cart->items as $item) {
                    $this->wishlistService->addItem($user, $item->product_id);
                }
            }

            $this->cartRepository->markAsExpired($cart);
        });
    }

    /**
     * Remove all items from the cart.
     *
     * @param  Cart  $cart  The cart to clear.
     */
    public function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
    }
}
