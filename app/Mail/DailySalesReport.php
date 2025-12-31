<?php

declare(strict_types=1);

namespace App\Mail;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * Daily sales report email sent to admin.
 *
 * Contains a summary of all products sold during the specified date,
 * including quantities, revenue, and order counts.
 */
class DailySalesReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param  Collection<int, object>  $productsSold  Collection of product sales data
     * @param  int  $totalOrders  Number of orders for the day
     * @param  float  $totalRevenue  Total revenue for the day
     * @param  int  $totalItemsSold  Total quantity of items sold
     * @param  CarbonInterface  $reportDate  The date of the report
     */
    public function __construct(
        public Collection $productsSold,
        public int $totalOrders,
        public float $totalRevenue,
        public int $totalItemsSold,
        public CarbonInterface $reportDate
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Sales Report - '.$this->reportDate->format('F j, Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-sales-report',
            text: 'emails.daily-sales-report-text',
            with: [
                'productsSold' => $this->productsSold,
                'totalOrders' => $this->totalOrders,
                'totalRevenue' => $this->totalRevenue,
                'totalItemsSold' => $this->totalItemsSold,
                'reportDate' => $this->reportDate->format('F j, Y'),
                'currencySymbol' => (string) config('app.currency_symbol', '€'),
                'appName' => (string) config('app.name'),
                'appUrl' => (string) config('app.url'),
            ],
        );
    }
}
