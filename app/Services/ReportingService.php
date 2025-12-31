<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\DailySalesReport;
use App\Repositories\Contracts\InvoiceContract;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

/**
 * Service class for generating and sending sales reports.
 *
 * Aggregates invoice data to provide insights on sales performance.
 */
class ReportingService
{
    public function __construct(
        private InvoiceContract $invoiceRepository
    ) {}

    /**
     * Generate a daily sales report for the specified date.
     *
     * Aggregates all invoices for a given day, calculating totals and
     * breaking down sales by product.
     *
     * @param  CarbonInterface|null  $date  The date to report on (defaults to today)
     * @return array{
     *     date: CarbonInterface,
     *     total_orders: int,
     *     total_items_sold: int,
     *     total_revenue: float,
     *     products: Collection<int, object>
     * }
     */
    public function getDailySalesReport(?CarbonInterface $date = null): array
    {
        $date = $date ?? now();
        $invoices = $this->invoiceRepository->findByDate($date);

        $products = [];
        $totalItemsSold = 0;
        $totalRevenue = 0.0;

        foreach ($invoices as $invoice) {
            $totalRevenue += (float) $invoice->total_amount;

            foreach ($invoice->items as $item) {
                $totalItemsSold += $item->quantity;
                $key = $item->product_name.'|'.$item->unit_price;

                if (! isset($products[$key])) {
                    $products[$key] = (object) [
                        'product_name' => $item->product_name,
                        'unit_price' => (float) $item->unit_price,
                        'total_quantity' => 0,
                        'total_revenue' => 0.0,
                    ];
                }

                $products[$key]->total_quantity += $item->quantity;
                $products[$key]->total_revenue += $item->quantity * (float) $item->unit_price;
            }
        }

        // Sort by revenue descending
        $sortedProducts = collect(array_values($products))
            ->sortByDesc('total_revenue')
            ->values();

        return [
            'date' => $date,
            'total_orders' => $invoices->count(),
            'total_items_sold' => $totalItemsSold,
            'total_revenue' => round($totalRevenue, 2),
            'products' => $sortedProducts,
        ];
    }

    /**
     * Send the daily sales report email to the admin.
     *
     * @param  CarbonInterface|null  $date  The date to report on (defaults to today)
     * @return bool True if email was sent, false if admin email not configured
     */
    public function sendDailySalesReport(?CarbonInterface $date = null): bool
    {
        $adminEmail = config('app.admin_email');

        if (! $adminEmail) {
            return false;
        }

        $report = $this->getDailySalesReport($date);

        Mail::to($adminEmail)->send(new DailySalesReport(
            productsSold: $report['products'],
            totalOrders: $report['total_orders'],
            totalRevenue: $report['total_revenue'],
            totalItemsSold: $report['total_items_sold'],
            reportDate: $report['date']
        ));

        return true;
    }
}
