<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Customer;
use App\Models\User;
use App\Repositories\Contracts\CustomerContract;

/**
 * Eloquent implementation of the customer repository.
 */
class CustomerRepository implements CustomerContract
{
    /**
     * {@inheritDoc}
     */
    public function findByUserId(int $userId): ?Customer
    {
        return Customer::query()->where('user_id', $userId)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function createOrUpdate(User $user, array $data): Customer
    {
        return Customer::query()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );
    }
}
