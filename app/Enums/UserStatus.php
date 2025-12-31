<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Represents the account status of a user.
 *
 * Controls whether a user can access the system and their level of access.
 */
enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
}
