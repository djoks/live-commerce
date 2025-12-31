<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Customer;
use App\Models\User;

/**
 * Contract for customer repository operations.
 *
 * Handles customer profile creation and retrieval.
 */
interface CustomerContract
{
    /**
     * Find a customer profile by user ID.
     */
    public function findByUserId(int $userId): ?Customer;

    /**
     * Create or update a customer profile for a user.
     *
     * @param  array<string, mixed>  $data  Customer profile data (address, phone, etc.)
     */
    public function createOrUpdate(User $user, array $data): Customer;
}
