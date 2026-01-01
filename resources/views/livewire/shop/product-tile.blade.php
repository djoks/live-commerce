<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;

/**
 * Product Tile Component.
 *
 * Displays individual product card with image, price, and add-to-cart functionality.
 */
new class extends Component {
    public Product $product;

    /**
     * Add the current product to the authenticated user's cart.
     * Redirects to login if user is not authenticated.
     * 
     * @param  CartService  $cartService
     */
    public function addToCart(CartService $cartService)
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $cartService->addItem(Auth::user(), $this->product->id);
        $this->dispatch('cart-updated');
    }
}
?>

<div class="group relative bg-[#F4F5F7] dark:bg-zinc-800 overflow-hidden cursor-pointer">
    {{-- Image --}}
    <div class="relative aspect-[285/301] w-full overflow-hidden">
        <img 
            src="{{ $product->getFirstMediaUrl('images') ?: 'https://placehold.co/285x301?text=No+Image' }}" 
            alt="{{ $product->name }}" 
            class="w-full h-full object-cover"
        >
        
        {{-- Hover Overlay --}}
        <div class="absolute inset-0 bg-white/70 dark:bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center p-4">
            <flux:button 
                wire:click="addToCart" 
                class="!bg-white !text-[#B88E2F] !border border-[#B88E2F] hover:!bg-[#B88E2F] hover:!text-white px-10 py-3 font-semibold transition-colors duration-300 cursor-pointer"
            >
                {{ __('Add to Cart') }}
            </flux:button>
        </div>
    </div>

    {{-- Product Info (clickable link to product page) --}}
    <a href="{{ route('shop.product', ['product' => $product]) }}" wire:navigate class="block p-4 pb-8">
        <h3 class="font-semibold text-2xl text-zinc-800 dark:text-zinc-100 mb-1 truncate">
            {{ $product->name }}
        </h3>
        <p class="text-[#898989] dark:text-zinc-400 font-medium text-base mb-2 truncate">
            {{ Str::limit($product->description, 30) }}
        </p>
        <div class="flex items-center justify-between">
            <span class="font-semibold text-xl text-zinc-800 dark:text-zinc-100">
                {{ config('app.currency_symbol') }} {{ number_format($product->price, 0, '.', '.') }}
            </span>
            <span class="text-sm {{ $product->stock_quantity <= config('app.low_stock_threshold') ? 'text-red-500' : 'text-[#898989] dark:text-zinc-400' }}">
                {{ $product->stock_quantity }} {{ __('in stock') }}
            </span>
        </div>
    </a>
</div>
