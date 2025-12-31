<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a random 50% of customers to receive invoices
        $customers = Customer::with('user')
            ->inRandomOrder()
            ->limit((int) ceil(Customer::count() * 0.5))
            ->get();

        foreach ($customers as $customer) {
            // Skip customers without a user
            if ($customer->user === null) {
                continue;
            }

            // Ensure customer has at least one payment method
            if ($customer->paymentMethods()->doesntExist()) {
                PaymentMethod::factory()->default()->create(['customer_id' => $customer->id]);
            }

            // Create 1-2 invoices per selected customer
            Invoice::factory(fake()->numberBetween(1, 2))
                ->has(InvoiceItem::factory()->count(fake()->numberBetween(2, 4)), 'items')
                ->create([
                    'user_id' => $customer->user_id,
                    'customer_email' => $customer->user->email,
                    'customer_name' => $customer->user->name,
                    'delivery_address' => $customer->delivery_address,
                    'city' => $customer->city,
                    'postal_code' => $customer->postal_code,
                    'country' => $customer->country,
                    'phone' => $customer->phone,
                ]);
        }
    }
}
