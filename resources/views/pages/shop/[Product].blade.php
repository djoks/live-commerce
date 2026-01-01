<?php

use App\Models\Product;
use App\Services\CartService;
use App\Services\ProductService;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use function Laravel\Folio\name;

name('shop.product');

/**
 * Product detail page component.
 *
 * Displays a single product with quantity selection and add-to-cart functionality.
 */
new class extends Component {
    public Product $product;
    public int $quantity = 1;

    /**
     * Mount the component with the product slug from the URL.
     *
     * Uses ProductService to fetch the product with all required relationships.
     */
    public function mount(Product $product): void
    {
        $this->product = $product->load('category', 'media');
    }

    /**
     * Increment the quantity if stock allows.
     */
    public function increment(): void
    {
        if ($this->quantity < $this->product->stock_quantity) {
            $this->quantity++;
        }
    }

    /**
     * Decrement the quantity (minimum 1).
     */
    public function decrement(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    /**
     * Add the current product to the user's cart.
     *
     * Redirects to login if user is not authenticated.
     */
    public function addToCart(CartService $cartService): void
    {
        if (! Auth::check()) {
            $this->redirect(route('login'));

            return;
        }

        $cartService->addItem(Auth::user(), $this->product->id, $this->quantity);
        $this->dispatch('cart-updated');
    }
};
?>

<x-layouts.storefront>
    {{-- Page Banner / Breadcrumbs --}}
    <livewire:page-banner title="Product Details" :links="[
        'Shop' => route('shop.index'),
        $product->name => '#',
    ]" />

    @volt
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {{-- Product Image --}}
            <div class="bg-[#F9F1E7] dark:bg-zinc-800 rounded-lg overflow-hidden">
                <img 
                    src="{{ $product->getFirstMediaUrl('images') ?: 'https://placehold.co/600x600?text=No+Image' }}" 
                    alt="{{ $product->name }}" 
                    class="w-full h-auto object-cover aspect-square"
                >
            </div>

            {{-- Product Details --}}
            <div class="flex flex-col">
                {{-- Name --}}
                <h1 class="font-poppins text-4xl font-semibold text-zinc-900 dark:text-white mb-2">
                    {{ $product->name }}
                </h1>

                {{-- Price --}}
                <p class="text-2xl text-[#9F9F9F] dark:text-zinc-400 mb-6">
                    {{ config('app.currency_symbol') }} {{ number_format($product->price, 2, '.', ',') }}
                </p>

                {{-- Description --}}
                <p class="text-zinc-600 dark:text-zinc-300 leading-relaxed mb-8">
                    {{ $product->description }}
                </p>

                {{-- Stock Status --}}
                <div class="mb-8">
                    <span class="text-sm {{ $product->stock_quantity <= config('app.low_stock_threshold') ? 'text-red-500' : 'text-green-600 dark:text-green-400' }}">
                        {{ $product->stock_quantity }} in stock
                    </span>
                </div>

                {{-- Quantity & Add to Cart --}}
                <div class="flex items-center gap-4 mb-8">
                    {{-- Quantity Selector --}}
                    <div class="flex items-center border border-zinc-300 dark:border-zinc-600 rounded-lg overflow-hidden">
                        <button 
                            wire:click="decrement"
                            class="px-4 py-3 text-xl text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors cursor-pointer"
                            @disabled($quantity <= 1)
                        >
                            −
                        </button>
                        <span class="px-6 py-3 text-lg font-medium text-zinc-900 dark:text-white min-w-[60px] text-center">
                            {{ $quantity }}
                        </span>
                        <button 
                            wire:click="increment"
                            class="px-4 py-3 text-xl text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors cursor-pointer"
                            @disabled($quantity >= $product->stock_quantity)
                        >
                            +
                        </button>
                    </div>

                    {{-- Add to Cart Button --}}
                    <button 
                        wire:click="addToCart"
                        class="px-10 py-3 border-2 border-zinc-900 dark:border-white text-zinc-900 dark:text-white font-medium rounded-lg hover:bg-zinc-900 dark:hover:bg-white hover:text-white dark:hover:text-zinc-900 transition-colors cursor-pointer"
                    >
                        {{ __('Add To Cart') }}
                    </button>
                </div>

                {{-- Divider --}}
                <hr class="border-zinc-200 dark:border-zinc-700 my-6">

                {{-- Product Meta --}}
                <div class="space-y-3 text-sm text-[#9F9F9F] dark:text-zinc-400">
                    {{-- Category --}}
                    <div class="flex items-center gap-4">
                        <span class="w-20">{{ __('Category') }}</span>
                        <span>:</span>
                        <span class="text-zinc-700 dark:text-zinc-300">{{ $product->category?->name ?? __('Uncategorized') }}</span>
                    </div>

                    {{-- Share --}}
                    <div class="flex items-center gap-4">
                        <span class="w-20">{{ __('Share') }}</span>
                        <span>:</span>
                        <div class="flex items-center gap-4">
                            <a href="https://facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="text-zinc-700 dark:text-zinc-300 hover:text-[#B88E2F] transition-colors">
                                <img src="{{ Vite::asset('resources/images/icons/facebook.svg') }}" alt="Facebook" class="w-5 h-5 dark:invert">
                            </a>
                            <a href="https://linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}" target="_blank" class="text-zinc-700 dark:text-zinc-300 hover:text-[#B88E2F] transition-colors">
                                <img src="{{ Vite::asset('resources/images/icons/linkedin.svg') }}" alt="LinkedIn" class="w-5 h-5 dark:invert">
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}" target="_blank" class="text-zinc-700 dark:text-zinc-300 hover:text-[#B88E2F] transition-colors">
                                <img src="{{ Vite::asset('resources/images/icons/x.svg') }}" alt="X" class="w-5 h-5 dark:invert">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endvolt
</x-layouts.storefront>
