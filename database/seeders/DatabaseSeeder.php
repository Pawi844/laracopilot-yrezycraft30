<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            ServiceSeeder::class,
            PlanSeeder::class,
            CustomerSeeder::class,
            TransactionSeeder::class,
            SupportTicketSeeder::class,
            NetworkZoneSeeder::class,
        ]);
    }
}