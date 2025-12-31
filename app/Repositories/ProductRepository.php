<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ProductSortOption;
use App\Models\Product;
use App\Repositories\Contracts\ProductContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Eloquent implementation of the product repository.
 *
 * Eager loads category and media relationships for all queries.
 */
class ProductRepository implements ProductContract
{
    /**
     * {@inheritDoc}
     */
    public function get(int $perPage = 15, ProductSortOption $sortBy = ProductSortOption::Default): LengthAwarePaginator
    {
        $query = Product::query()
            ->active()
            ->with('category', 'media');

        $this->applySorting($query, $sortBy);

        return $query->paginate($perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function getLatest(int $limit = 8): Collection
    {
        return Product::query()
            ->active()
            ->with('category', 'media')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getAll(): Collection
    {
        return Product::query()
            ->active()
            ->with('category', 'media')
            ->orderBy('name')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findBySlug(string $slug): ?Product
    {
        return Product::query()
            ->with('category', 'media')
            ->where('slug', $slug)
            ->first();
    }

    /**
     * {@inheritDoc}
     */
    public function getByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return Product::query()
            ->active()
            ->byCategory($categoryId)
            ->with('category', 'media')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function getAllByCategory(int $categoryId): Collection
    {
        return Product::query()
            ->active()
            ->byCategory($categoryId)
            ->with('category', 'media')
            ->orderBy('name')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function reduceStock(Product $product, int $quantity): void
    {
        $product->reduceStock($quantity);
    }

    /**
     * Apply sorting to a product query.
     *
     * @param  Builder<Product>  $query
     */
    private function applySorting(Builder $query, ProductSortOption $sortBy): void
    {
        match ($sortBy) {
            ProductSortOption::Newest => $query->latest(),
            ProductSortOption::PriceAsc => $query->orderBy('price', 'asc'),
            ProductSortOption::PriceDesc => $query->orderBy('price', 'desc'),
            ProductSortOption::Default => $query->latest(),
        };
    }
}
