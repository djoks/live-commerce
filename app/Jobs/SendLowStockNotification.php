<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\LowStockNotification;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

/**
 * Job to send low stock notification emails to the admin.
 *
 * Dispatched when a product's stock falls below the configured threshold.
 * Sends an email alert to the admin configured in the application settings.
 */
class SendLowStockNotification implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Product $product
    ) {}

    /**
     * Execute the job.
     *
     * Sends a low stock notification email to the configured admin address.
     */
    public function handle(): void
    {
        $adminEmail = config('app.admin_email');

        if (! $adminEmail) {
            return;
        }

        Mail::to($adminEmail)->send(new LowStockNotification($this->product));
    }
}
