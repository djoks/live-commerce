<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\InvoiceContract;
use Carbon\CarbonInterface;

/**
 * Service class for generating sales reports.
 *
 * Aggregates invoice data to provide insights on sales performance.
 */
class ReportingService
{
    public function __construct(
        private InvoiceContract $invoiceRepository
    ) {}

    /**
     * Generate a daily sales report.
     *
     * Aggregates all invoices for a given day, calculating totals and
     * breaking down sales by product.
     *
     * @param  CarbonInterface|null  $date  The date to report on (defaults to today)
     * @return array{date: string, total_invoices: int, total_items_sold: int, total_revenue: float, products: array<int, array{name: string, quantity: int, revenue: float}>}
     */
    public function getDailySalesReport(?CarbonInterface $date = null): array
    {
        $date = $date ?? now();
        $invoices = $this->invoiceRepository->findTodaysInvoices();

        $products = [];
        $totalItemsSold = 0;
        $totalRevenue = 0.0;

        foreach ($invoices as $invoice) {
            $totalRevenue += (float) $invoice->total_amount;

            foreach ($invoice->items as $item) {
                $totalItemsSold += $item->quantity;
                $productId = $item->product_id;

                if (! isset($products[$productId])) {
                    $products[$productId] = [
                        'name' => $item->product_name,
                        'quantity' => 0,
                        'revenue' => 0.0,
                    ];
                }

                $products[$productId]['quantity'] += $item->quantity;
                $products[$productId]['revenue'] += $item->quantity * (float) $item->unit_price;
            }
        }

        return [
            'date' => $date->toDateString(),
            'total_invoices' => $invoices->count(),
            'total_items_sold' => $totalItemsSold,
            'total_revenue' => round($totalRevenue, 2),
            'products' => array_values($products),
        ];
    }
}
