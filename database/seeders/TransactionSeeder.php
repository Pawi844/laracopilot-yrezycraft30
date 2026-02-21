<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Plan;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $customers = Customer::all();
        $plans = Plan::all();
        $methods = ['mpesa', 'mpesa', 'mpesa', 'bank_transfer', 'cash', 'card'];
        $statuses = ['completed', 'completed', 'completed', 'completed', 'pending', 'failed'];

        for ($i = 0; $i < 40; $i++) {
            $customer = $customers->random();
            $plan = $plans->random();
            $method = $methods[array_rand($methods)];
            $status = $statuses[array_rand($statuses)];

            Transaction::create([
                'customer_id' => $customer->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'payment_method' => $method,
                'reference_number' => strtoupper($method === 'mpesa' ? 'MP' : 'TXN') . rand(100000, 999999),
                'status' => $status,
                'notes' => $status === 'failed' ? 'Payment declined by provider' : null,
                'created_at' => now()->subDays(rand(0, 90))
            ]);
        }
    }
}