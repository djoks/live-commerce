<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ProductSortOption;
use App\Models\Product;
use App\Repositories\Contracts\ProductContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service class for product operations.
 *
 * Provides a clean interface for retrieving and managing products.
 * All product queries should go through this service.
 */
class ProductService
{
    public function __construct(
        private ProductContract $productRepository
    ) {}

    /**
     * Get paginated products with sorting.
     *
     * @return LengthAwarePaginator<int, Product>
     */
    public function getPaginated(int $perPage = 15, ProductSortOption $sortBy = ProductSortOption::Default): LengthAwarePaginator
    {
        return $this->productRepository->get($perPage, $sortBy);
    }

    /**
     * Get the latest products for display on the home page.
     *
     * @return Collection<int, Product>
     */
    public function getLatest(int $limit = 8): Collection
    {
        return $this->productRepository->getLatest($limit);
    }

    /**
     * Get all active products.
     *
     * @return Collection<int, Product>
     */
    public function getAll(): Collection
    {
        return $this->productRepository->getAll();
    }

    /**
     * Find a product by its slug.
     */
    public function findBySlug(string $slug): ?Product
    {
        return $this->productRepository->findBySlug($slug);
    }

    /**
     * Get paginated products in a specific category.
     *
     * @return LengthAwarePaginator<int, Product>
     */
    public function getByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->getByCategory($categoryId, $perPage);
    }

    /**
     * Get all products in a category.
     *
     * @return Collection<int, Product>
     */
    public function getAllByCategory(int $categoryId): Collection
    {
        return $this->productRepository->getAllByCategory($categoryId);
    }
}
