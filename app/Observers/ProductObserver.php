<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\SendLowStockNotification;
use App\Models\Product;

/**
 * Observer for Product model events.
 *
 * Monitors stock changes and dispatches low stock notifications
 * when inventory falls below the configured threshold.
 */
class ProductObserver
{
    /**
     * Handle the Product "updated" event.
     *
     * Checks for stock level changes and dispatches notifications
     * when stock falls below threshold. Resets notification flag
     * when stock is replenished above threshold.
     */
    public function updated(Product $product): void
    {
        if (! $product->wasChanged('stock_quantity')) {
            return;
        }

        $threshold = (int) config('app.low_stock_threshold', 10);
        $currentStock = $product->stock_quantity;

        // Stock replenished above threshold - reset notification flag
        if ($currentStock > $threshold && $product->low_stock_notified_at !== null) {
            $product->updateQuietly(['low_stock_notified_at' => null]);

            return;
        }

        // Stock is low, not depleted, and not yet notified
        if ($currentStock <= $threshold && $currentStock > 0 && $product->low_stock_notified_at === null) {
            SendLowStockNotification::dispatch($product);
            $product->updateQuietly(['low_stock_notified_at' => now()]);
        }
    }
}
