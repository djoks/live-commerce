<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PaymentMethod Model
 *
 * Represents a saved payment method for a customer. Payment details
 * are encrypted at rest for security.
 */
class PaymentMethod extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentMethodFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'type',
        'label',
        'details',
        'is_default',
    ];

    /**
     * Define attribute casting for the model.
     *
     * Payment details are encrypted for security.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'details' => 'encrypted:array',
            'is_default' => 'boolean',
        ];
    }

    /**
     * Get the customer who owns this payment method.
     *
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Scope to payment methods marked as the customer's default.
     *
     * @param  Builder<PaymentMethod>  $query
     * @return Builder<PaymentMethod>
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }
}
