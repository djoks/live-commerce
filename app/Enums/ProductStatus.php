<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Represents the visibility status of a product in the store.
 *
 * Products start as Draft, become Active when published, and can be Archived when discontinued.
 */
enum ProductStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Archived = 'archived';
}
