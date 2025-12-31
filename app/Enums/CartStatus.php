<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Represents the lifecycle status of a shopping cart.
 *
 * Carts start as Active, can Expire after inactivity, or become Sold after checkout.
 */
enum CartStatus: string
{
    case Active = 'active';
    case Expired = 'expired';
    case Sold = 'sold';
}
