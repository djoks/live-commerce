<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for category repository operations.
 *
 * Defines how to fetch and find product categories.
 */
interface CategoryContract
{
    /**
     * Get all categories.
     *
     * @return Collection<int, Category>
     */
    public function all(): Collection;

    /**
     * Find a category by its URL slug.
     */
    public function findBySlug(string $slug): ?Category;
}
