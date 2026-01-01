<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure test user has customer profile
        $user = User::where('email', 'test@example.com')->first();
        if ($user && ! $user->customer) {
            Customer::factory()->create(['user_id' => $user->id]);
        }

        // Create 10 customers (users + profiles)
        $customers = Customer::factory(10)->create();

        // For 5 of them, create 1-3 invoices
        $customers->take(5)->each(function ($customer) {
            /** @var User $user */
            $user = $customer->user;
            \App\Models\Invoice::factory(rand(1, 3))->create([
                'user_id' => $customer->user_id,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
            ]);
        });
    }
}
