<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Customer Model
 *
 * Represents extra profile information for a user, such as shipping details.
 * Linked 1:1 with the User model.
 */
class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'delivery_address',
        'city',
        'postal_code',
        'country',
        'phone',
    ];

    /**
     * Get the user account associated with this customer profile.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all payment methods saved by this customer.
     *
     * @return HasMany<PaymentMethod, $this>
     */
    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }

    /**
     * Scope to customers who have a delivery address set.
     *
     * Useful for filtering customers who can receive shipments.
     *
     * @param  Builder<Customer>  $query
     * @return Builder<Customer>
     */
    public function scopeWithDeliveryAddress(Builder $query): Builder
    {
        return $query->whereNotNull('delivery_address');
    }
}
