<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CartStatus;
use App\Enums\CartType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Cart Model
 *
 * Represents a user's shopping cart or wishlist.
 *
 * @property \Carbon\Carbon|null $expires_at
 */
class Cart extends Model
{
    /** @use HasFactory<\Database\Factories\CartFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'name',
        'expires_at',
    ];

    /**
     * Define attribute casting for the model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => CartType::class,
            'status' => CartStatus::class,
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Get the user who owns this cart.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all line items in this cart.
     *
     * @return HasMany<CartItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Scope the query to only include carts of a specific type.
     *
     * @param  Builder<Cart>  $query
     * @return Builder<Cart>
     */
    public function scopeOfType(Builder $query, CartType $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope the query to only include active carts.
     *
     * @param  Builder<Cart>  $query
     * @return Builder<Cart>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CartStatus::Active);
    }

    /**
     * Scope the query to only include expired carts.
     *
     * @param  Builder<Cart>  $query
     * @return Builder<Cart>
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', CartStatus::Expired);
    }

    /**
     * Scope the query to include carts that are active but past their expiration time.
     *
     * @param  Builder<Cart>  $query
     * @return Builder<Cart>
     */
    public function scopePendingExpiration(Builder $query): Builder
    {
        return $query->active()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }
}
