<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Product Model
 *
 * Represents a product available for sale. Products belong to categories,
 * have stock tracking, and support media attachments for images.
 */
class Product extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    use HasSlug;
    use InteractsWithMedia;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock_quantity',
        'status',
    ];

    /**
     * Define attribute casting for the model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'status' => ProductStatus::class,
        ];
    }

    /**
     * Configure slug generation options.
     *
     * Slugs are auto-generated from the product name for SEO-friendly URLs.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the route key for the model.
     *
     * Uses slug for SEO-friendly URLs instead of ID.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Register available media collections for this model.
     *
     * Products have an 'images' collection for product photos.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
    }

    /**
     * Get the category this product belongs to.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Reduce the product's stock by a given quantity.
     *
     * Used when items are purchased or reserved.
     */
    public function reduceStock(int $quantity): void
    {
        $this->decrement('stock_quantity', $quantity);
    }

    /**
     * Scope to products that have available stock.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Scope to products running low on stock but not out of stock.
     *
     * @param  Builder<Product>  $query
     * @param  int  $threshold  Stock level considered "low" (default: 5)
     * @return Builder<Product>
     */
    public function scopeLowStock(Builder $query, int $threshold = 5): Builder
    {
        return $query->where('stock_quantity', '<=', $threshold)
            ->where('stock_quantity', '>', 0);
    }

    /**
     * Scope to products in a specific category.
     *
     * @param  Builder<Product>  $query
     * @param  int  $categoryId  The category ID to filter by
     * @return Builder<Product>
     */
    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to products that are active and visible in the store.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ProductStatus::Active);
    }
}
