<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Represents the payment status of an invoice.
 *
 * Tracks the invoice through its lifecycle from creation to final resolution.
 */
enum InvoiceStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
}
