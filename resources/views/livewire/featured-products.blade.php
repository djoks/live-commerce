<?php

use App\Services\ProductService;
use Livewire\Volt\Component;

/**
 * Featured products component for the home page.
 *
 * Displays the latest products with a "Show More" link to the shop.
 */
new class extends Component {
    /**
     * Compute data for the view.
     *
     * @return array<string, mixed>
     */
    public function with(ProductService $productService): array
    {
        return [
            'products' => $productService->getLatest(8),
        ];
    }
};
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
    <div class="text-center mb-12">
        <h2 class="font-bold text-3xl md:text-[40px] text-zinc-800 dark:text-white mb-4">Browse The Range</h2>
        <p class="text-zinc-600 dark:text-zinc-400 text-lg md:text-xl">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 mb-12">
        @foreach($products as $product)
            <livewire:shop.product-tile :product="$product" :key="$product->id" />
        @endforeach
    </div>

    <div class="text-center">
        <a href="{{ route('shop.index') }}" class="inline-block border border-[#B88E2F] text-[#B88E2F] hover:bg-[#B88E2F] hover:text-white font-semibold py-3 px-[74px] transition-colors duration-300 rounded-[10px]">
            Show More
        </a>
    </div>
</div>
