<?php

use App\Enums\ProductSortOption;
use App\Services\ProductService;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

/**
 * Main Shop Product Listing Component.
 *
 * Handles fetching, filtering, sorting, and pagination of products.
 * Integrates with the `filter-bar` and `product-tile` components.
 */
new class extends Component {
    use WithPagination;

    #[Url]
    public int $perPage = 16;

    #[Url]
    public string $sortBy = 'default';

    #[On('filterChanged')]
    public function handleFilterChanged(int $perPage, string $sortBy): void
    {
        $this->perPage = $perPage;
        $this->sortBy = $sortBy;
        $this->resetPage();
    }

    /**
     * Get the sort option enum from the URL string value.
     */
    private function getSortOption(): ProductSortOption
    {
        return ProductSortOption::tryFrom($this->sortBy) ?? ProductSortOption::Default;
    }

    /**
     * Compute data for the view.
     *
     * @return array<string, mixed>
     */
    public function with(ProductService $productService): array
    {
        return [
            'products' => $productService->getPaginated($this->perPage, $this->getSortOption()),
        ];
    }
};
?>

<div>
    {{-- Filter Bar Component --}}
    <livewire:filter-bar 
        :key="'filter-bar-' . $perPage . '-' . $sortBy . '-' . ($products->firstItem() ?? 0)"
        :perPage="$perPage"
        :sortBy="$sortBy"
        :firstItem="$products->firstItem() ?? 0"
        :lastItem="$products->lastItem() ?? 0"
        :total="$products->total()"
    />

    {{-- Product Grid --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach($products as $product)
                <livewire:shop.product-tile :product="$product" :key="$product->id" />
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-12 flex justify-center">
            {{ $products->links('vendor.pagination.shop') }}
        </div>
    </div>
</div>
