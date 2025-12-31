<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\ProductSortOption;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for product repository operations.
 *
 * Handles product retrieval, filtering, and stock management.
 */
interface ProductContract
{
    /**
     * Get paginated list of active products with optional sorting.
     *
     * @return LengthAwarePaginator<int, Product>
     */
    public function get(int $perPage = 15, ProductSortOption $sortBy = ProductSortOption::Default): LengthAwarePaginator;

    /**
     * Get the latest active products.
     *
     * @return Collection<int, Product>
     */
    public function getLatest(int $limit = 8): Collection;

    /**
     * Get all active products without pagination.
     *
     * @return Collection<int, Product>
     */
    public function getAll(): Collection;

    /**
     * Find a product by its URL slug.
     */
    public function findBySlug(string $slug): ?Product;

    /**
     * Get paginated products filtered by category.
     *
     * @return LengthAwarePaginator<int, Product>
     */
    public function getByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all products in a category without pagination.
     *
     * @return Collection<int, Product>
     */
    public function getAllByCategory(int $categoryId): Collection;

    /**
     * Reduce a product's stock quantity.
     */
    public function reduceStock(Product $product, int $quantity): void;
}
