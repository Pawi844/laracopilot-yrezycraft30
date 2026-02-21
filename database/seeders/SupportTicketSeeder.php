<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupportTicket;
use App\Models\Customer;

class SupportTicketSeeder extends Seeder
{
    public function run()
    {
        $customers = Customer::all();
        $subjects = [
            'Internet connection is slow', 'Cannot connect to WiFi', 'Billing query',
            'Request to upgrade plan', 'Installation appointment request',
            'Router not working', 'High latency issues', 'Request for static IP',
            'Payment not reflecting', 'Service outage in my area'
        ];
        $statuses = ['open', 'open', 'in_progress', 'resolved', 'closed'];
        $priorities = ['low', 'medium', 'medium', 'high', 'urgent'];

        for ($i = 0; $i < 20; $i++) {
            $customer = $customers->random();
            SupportTicket::create([
                'customer_id' => $customer->id,
                'name' => $customer->full_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'subject' => $subjects[array_rand($subjects)],
                'message' => 'I am experiencing issues with my internet connection. Please assist me as soon as possible. This is affecting my work significantly.',
                'status' => $statuses[array_rand($statuses)],
                'priority' => $priorities[array_rand($priorities)],
                'admin_notes' => null,
                'created_at' => now()->subDays(rand(0, 30))
            ]);
        }
    }
}