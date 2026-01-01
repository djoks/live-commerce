<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Invoice;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for invoice repository operations.
 *
 * Handles order/invoice creation and retrieval.
 */
interface InvoiceContract
{
    /**
     * Create a new invoice with line items.
     *
     * @param  array<string, mixed>  $invoiceData  Invoice header data (totals, address, etc.)
     * @param  array<int, array<string, mixed>>  $items  Line items to attach
     */
    public function create(array $invoiceData, array $items): Invoice;

    /**
     * Get all invoices created today.
     *
     * @return Collection<int, Invoice>
     */
    public function findTodaysInvoices(): Collection;

    /**
     * Get all invoices for a specific date.
     *
     * @return Collection<int, Invoice>
     */
    public function findByDate(CarbonInterface $date): Collection;

    /**
     * Get paginated invoices for a specific user.
     *
     * @return LengthAwarePaginator<int, Invoice>
     */
    public function getByUserId(int $userId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all invoices for a user without pagination.
     *
     * @return Collection<int, Invoice>
     */
    public function getAllByUserId(int $userId): Collection;

    /**
     * Check if an order number already exists.
     */
    public function orderNumberExists(string $orderNo): bool;
}
