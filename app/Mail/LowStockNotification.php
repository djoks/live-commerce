<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Low stock notification email sent to admin.
 *
 * Alerts the admin when a product's stock falls below the configured threshold.
 */
class LowStockNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Product $product
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Low Stock Alert: '.$this->product->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.low-stock-notification',
            text: 'emails.low-stock-notification-text',
            with: [
                'productName' => $this->product->name,
                'productSlug' => $this->product->slug,
                'currentStock' => $this->product->stock_quantity,
                'threshold' => (int) config('app.low_stock_threshold'),
                'appName' => (string) config('app.name'),
                'appUrl' => (string) config('app.url'),
            ],
        );
    }
}
