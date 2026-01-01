<?php

use Livewire\Volt\Component;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use Livewire\Attributes\On;

new class extends Component {
    public ?Cart $cart = null;

    /**
     * Initialize the cart drawer with the user's active cart.
     */
    public function mount(CartService $cartService): void
    {
        if (Auth::check()) {
            $this->cart = $cartService->getOrCreateActiveCart(Auth::user());
        }
    }

    /**
     * Refresh the cart from the database when a cart update event is triggered.
     */
    #[On('cart-updated')]
    public function refreshCart(CartService $cartService): void
    {
        if (Auth::check()) {
            $this->cart = $cartService->getOrCreateActiveCart(Auth::user());
        }
    }

    /**
     * Update the quantity of an item in the cart.
     *
     * @param  int  $productId  The product ID to update.
     * @param  int  $quantity  The new quantity.
     */
    public function updateQuantity(int $productId, int $quantity, CartService $cartService): void
    {
        if (!Auth::check()) return;

        if ($quantity < 1) {
            $this->removeItem($productId, $cartService);
            return;
        }

        $cartService->updateItemQuantity(Auth::user(), $productId, $quantity);
        $this->refreshCart($cartService);
        $this->dispatch('cart-updated');
    }

    /**
     * Remove an item from the cart.
     *
     * @param  int  $productId  The product ID to remove.
     */
    public function removeItem(int $productId, CartService $cartService): void
    {
        if (!Auth::check()) return;

        $cartService->removeItem(Auth::user(), $productId);
        $this->refreshCart($cartService);
        $this->dispatch('cart-updated');
    }

    /**
     * Clear all items from the cart.
     */
    public function clearCart(CartService $cartService): void
    {
        if (!Auth::check()) return;

        $cartService->clearCart($this->cart);
        $this->refreshCart($cartService);
        $this->dispatch('cart-updated');
    }

    /**
     * Get the cart subtotal.
     *
     * @return float The sum of all item prices multiplied by quantities.
     */
    public function getSubtotalProperty(): float
    {
        if (!$this->cart || !$this->cart->items) {
            return 0.0;
        }

        return $this->cart->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
    }
}; ?>

<div 
    x-data="{ open: false }"
    @toggle-cart-drawer.window="open = !open"
    class="relative z-50"
    aria-labelledby="slide-over-title" 
    role="dialog" 
    aria-modal="true"
    x-cloak
>
    {{-- Backdrop --}}
    <div 
        x-show="open"
        x-transition:enter="ease-in-out duration-500"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in-out duration-500"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500/75 transition-opacity" 
        aria-hidden="true"
        @click="open = false"
    ></div>

    <div class="fixed inset-0 overflow-hidden" x-show="open">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div 
                    x-show="open"
                    x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto w-screen max-w-md"
                >
                    <div class="flex h-full flex-col overflow-y-scroll bg-white dark:bg-zinc-900 shadow-xl">
                        {{-- Header --}}
                        <div class="flex items-start justify-between px-4 py-6 sm:px-6 border-b border-zinc-200 dark:border-zinc-700">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white" id="slide-over-title">{{ __('Cart') }}</h2>
                                @if($cart && $cart->items->isNotEmpty() && $cart->expires_at)
                                    <x-cart.expiration-timer :expiresAt="$cart->expires_at->toIso8601String()" />
                                @endif
                            </div>
                            <div class="ml-3 flex h-7 items-center">
                                <button type="button" class="relative -m-2 p-2 text-gray-400 hover:text-gray-500" @click="open = false">
                                    <span class="absolute -inset-0.5"></span>
                                    <span class="sr-only">{{ __('Close panel') }}</span>
                                    <flux:icon name="x-mark" class="h-6 w-6" />
                                </button>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6">
                            @if(!$cart || $cart->items->isEmpty())
                                <div class="flex flex-col items-center justify-center h-full text-center space-y-4">
                                    <flux:icon name="shopping-bag" class="h-16 w-16 text-zinc-300" />
                                    <p class="text-lg text-zinc-500">{{ __('Your cart is empty.') }}</p>
                                </div>
                            @else
                                <ul role="list" class="-my-6 divide-y divide-gray-200 dark:divide-zinc-700">
                                    @foreach($cart->items as $item)
                                        <li class="flex py-6 items-center">
                                            <div class="h-24 w-24 shrink-0 overflow-hidden rounded-md border border-gray-200 dark:border-zinc-700">
                                                <img 
                                                    src="{{ $item->product->getFirstMediaUrl('images') ?: 'https://placehold.co/100x100?text=No+Image' }}" 
                                                    alt="{{ $item->product->name }}" 
                                                    class="h-full w-full object-cover object-center"
                                                >
                                            </div>

                                            <div class="ml-4 flex flex-1 items-center justify-between">
                                                <div class="flex flex-col gap-3">
                                                    <h3 class="text-base font-medium text-gray-900 dark:text-white">
                                                        <a href="{{ route('shop.product', ['product' => $item->product]) }}">{{ $item->product->name }}</a>
                                                    </h3>
                                                    <div class="flex items-center gap-2 text-sm">
                                                        <input 
                                                            type="number" 
                                                            min="1" 
                                                            value="{{ $item->quantity }}" 
                                                            wire:change="updateQuantity({{ $item->product_id }}, $event.target.value)"
                                                            class="w-16 p-1 text-center border border-zinc-300 dark:border-zinc-600 rounded-md bg-transparent text-zinc-900 dark:text-white"
                                                        >
                                                        <p class="text-zinc-500 dark:text-zinc-400">x</p>
                                                        <p class="text-amber-600 font-medium text-lg">
                                                            {{ config('app.currency_symbol') }} {{ number_format($item->product->price, 0, '.', '.') }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <button 
                                                    type="button" 
                                                    wire:click="removeItem({{ $item->product_id }})"
                                                    class="font-medium text-zinc-400 hover:text-red-500 ml-4"
                                                >
                                                    <flux:icon name="x-circle" class="h-5 w-5" variant="solid" />
                                                </button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        {{-- Footer --}}
                        @if($cart && $cart->items->isNotEmpty())
                            <div class="border-t border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                                <div class="flex justify-between text-base font-medium text-gray-900 dark:text-white px-4 py-6 sm:px-6">
                                    <p>{{ __('Subtotal') }}</p>
                                    <p class="text-amber-600">{{ config('app.currency_symbol') }} {{ number_format($this->subtotal, 0, '.', '.') }}</p>
                                </div>
                                <div class="flex gap-4 border-t border-zinc-200 dark:border-zinc-700 px-4 py-6 sm:px-6">
                                    <button 
                                        type="button" 
                                        wire:click="clearCart"
                                        wire:confirm="{{ __('Are you sure you want to clear your cart?') }}"
                                        class="flex-1 flex items-center justify-center rounded-lg border border-black dark:border-white px-6 py-3 text-base font-medium text-black dark:text-white hover:bg-red-50 hover:text-red-600 hover:border-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-400 dark:hover:border-red-400 transition-colors"
                                    >
                                        {{ __('Clear') }}
                                    </button>
                                    <x-primary-button href="{{ route('checkout') }}" class="flex-1 py-3 px-6">
                                        {{ __('Checkout') }}
                                    </x-primary-button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
