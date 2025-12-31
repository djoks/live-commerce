<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryContract;
use Illuminate\Database\Eloquent\Collection;

/**
 * Eloquent implementation of the category repository.
 */
class CategoryRepository implements CategoryContract
{
    /**
     * {@inheritDoc}
     */
    public function all(): Collection
    {
        return Category::query()->orderBy('name')->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findBySlug(string $slug): ?Category
    {
        return Category::query()->where('slug', $slug)->first();
    }
}
