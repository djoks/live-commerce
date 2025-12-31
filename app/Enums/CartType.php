<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Represents the type of cart a customer can have.
 *
 * A cart can either be a regular shopping cart or a wishlist for saving items.
 */
enum CartType: string
{
    case Cart = 'cart';
    case Wishlist = 'wishlist';
}
