<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceStatus;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Invoice Model
 *
 * Represents a completed order with billing and shipping details.
 * Contains a snapshot of customer info and line items at time of purchase.
 */
class Invoice extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'delivery_address',
        'city',
        'postal_code',
        'country',
        'phone',
        'sub_total',
        'discount',
        'tax',
        'total_amount',
        'status',
        'order_no',
    ];

    /**
     * Define attribute casting for the model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sub_total' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'status' => InvoiceStatus::class,
        ];
    }

    /**
     * Get the user who placed this order.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all line items on this invoice.
     *
     * @return HasMany<InvoiceItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Scope to invoices created today.
     *
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope to invoices created within a date range.
     *
     * @param  Builder<Invoice>  $query
     * @param  CarbonInterface  $from  Start of date range (inclusive)
     * @param  CarbonInterface  $to  End of date range (inclusive)
     * @return Builder<Invoice>
     */
    public function scopeBetweenDates(Builder $query, CarbonInterface $from, CarbonInterface $to): Builder
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope to invoices that have been paid.
     *
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::Paid);
    }

    /**
     * Scope to invoices awaiting payment.
     *
     * @param  Builder<Invoice>  $query
     * @return Builder<Invoice>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::Pending);
    }
}
