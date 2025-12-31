<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if ($user && $user->customer) {
            PaymentMethod::factory(2)->create(['customer_id' => $user->customer->id]);
            PaymentMethod::factory()->default()->create(['customer_id' => $user->customer->id]);
        }
    }
}
