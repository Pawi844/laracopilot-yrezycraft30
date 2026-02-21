<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Plan;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $plans = Plan::all();
        $counties = ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika', 'Malindi', 'Kitale', 'Garissa', 'Nyeri'];
        $statuses = ['active', 'active', 'active', 'active', 'inactive', 'suspended', 'pending'];

        $customers = [
            ['first_name' => 'James', 'last_name' => 'Mwangi', 'email' => 'james.mwangi@gmail.com', 'phone' => '+254712345678', 'id_number' => '12345678', 'address' => 'Westlands, Nairobi', 'county' => 'Nairobi', 'status' => 'active'],
            ['first_name' => 'Grace', 'last_name' => 'Achieng', 'email' => 'grace.achieng@gmail.com', 'phone' => '+254723456789', 'id_number' => '23456789', 'address' => 'Nyali, Mombasa', 'county' => 'Mombasa', 'status' => 'active'],
            ['first_name' => 'David', 'last_name' => 'Kimani', 'email' => 'david.kimani@yahoo.com', 'phone' => '+254734567890', 'id_number' => '34567890', 'address' => 'Kilimani, Nairobi', 'county' => 'Nairobi', 'status' => 'active'],
            ['first_name' => 'Mary', 'last_name' => 'Otieno', 'email' => 'mary.otieno@gmail.com', 'phone' => '+254745678901', 'id_number' => '45678901', 'address' => 'Milimani, Kisumu', 'county' => 'Kisumu', 'status' => 'active'],
            ['first_name' => 'Peter', 'last_name' => 'Njoroge', 'email' => 'peter.njoroge@gmail.com', 'phone' => '+254756789012', 'id_number' => '56789012', 'address' => 'Bahati, Nakuru', 'county' => 'Nakuru', 'status' => 'inactive'],
            ['first_name' => 'Sarah', 'last_name' => 'Wanjiku', 'email' => 'sarah.wanjiku@gmail.com', 'phone' => '+254767890123', 'id_number' => '67890123', 'address' => 'Parklands, Nairobi', 'county' => 'Nairobi', 'status' => 'active'],
            ['first_name' => 'John', 'last_name' => 'Kamau', 'email' => 'john.kamau@outlook.com', 'phone' => '+254778901234', 'id_number' => '78901234', 'address' => 'South B, Nairobi', 'county' => 'Nairobi', 'status' => 'suspended'],
            ['first_name' => 'Agnes', 'last_name' => 'Mutua', 'email' => 'agnes.mutua@gmail.com', 'phone' => '+254789012345', 'id_number' => '89012345', 'address' => 'Tudor, Mombasa', 'county' => 'Mombasa', 'status' => 'active'],
            ['first_name' => 'Samuel', 'last_name' => 'Korir', 'email' => 'samuel.korir@gmail.com', 'phone' => '+254790123456', 'id_number' => '90123456', 'address' => 'Huruma, Eldoret', 'county' => 'Eldoret', 'status' => 'active'],
            ['first_name' => 'Faith', 'last_name' => 'Chebet', 'email' => 'faith.chebet@gmail.com', 'phone' => '+254701234567', 'id_number' => '01234567', 'address' => 'Thika Road, Nairobi', 'county' => 'Nairobi', 'status' => 'pending'],
            ['first_name' => 'Michael', 'last_name' => 'Odhiambo', 'email' => 'michael.odhiambo@gmail.com', 'phone' => '+254711234567', 'id_number' => '11234567', 'address' => 'Kisumu West', 'county' => 'Kisumu', 'status' => 'active'],
            ['first_name' => 'Lucy', 'last_name' => 'Githui', 'email' => 'lucy.githui@gmail.com', 'phone' => '+254722345678', 'id_number' => '22345678', 'address' => 'Kiambu Road, Nairobi', 'county' => 'Nairobi', 'status' => 'active'],
            ['first_name' => 'Daniel', 'last_name' => 'Baraza', 'email' => 'daniel.baraza@gmail.com', 'phone' => '+254733456789', 'id_number' => '33456789', 'address' => 'Old Town, Mombasa', 'county' => 'Mombasa', 'status' => 'active'],
            ['first_name' => 'Elizabeth', 'last_name' => 'Wachira', 'email' => 'elizabeth.wachira@gmail.com', 'phone' => '+254744567890', 'id_number' => '44567890', 'address' => 'Kasarani, Nairobi', 'county' => 'Nairobi', 'status' => 'active'],
            ['first_name' => 'Paul', 'last_name' => 'Nyamweya', 'email' => 'paul.nyamweya@gmail.com', 'phone' => '+254755678901', 'id_number' => '55678901', 'address' => 'Kisii Town', 'county' => 'Nakuru', 'status' => 'active'],
        ];

        foreach ($customers as $data) {
            $data['plan_id'] = $plans->isNotEmpty() ? $plans->random()->id : null;
            Customer::create($data);
        }
    }
}