<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Invoice;
use App\Repositories\Contracts\InvoiceContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Eloquent implementation of the invoice repository.
 *
 * Uses database transactions for invoice creation to ensure data integrity.
 */
class InvoiceRepository implements InvoiceContract
{
    /**
     * {@inheritDoc}
     *
     * Wraps creation in a transaction to ensure invoice and items are saved together.
     */
    public function create(array $invoiceData, array $items): Invoice
    {
        return DB::transaction(function () use ($invoiceData, $items) {
            $invoice = Invoice::query()->create($invoiceData);

            foreach ($items as $item) {
                $invoice->items()->create($item);
            }

            return $invoice->load('items');
        });
    }

    /**
     * {@inheritDoc}
     */
    public function findTodaysInvoices(): Collection
    {
        return Invoice::query()
            ->today()
            ->with('items')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getByUserId(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Invoice::query()
            ->where('user_id', $userId)
            ->with('items')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function getAllByUserId(int $userId): Collection
    {
        return Invoice::query()
            ->where('user_id', $userId)
            ->with('items')
            ->latest()
            ->get();
    }
}
