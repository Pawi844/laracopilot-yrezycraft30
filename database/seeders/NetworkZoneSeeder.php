<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NetworkZone;

class NetworkZoneSeeder extends Seeder
{
    public function run()
    {
        $zones = [
            ['county' => 'Nairobi', 'area' => 'Westlands', 'coverage_type' => '5g', 'status' => 'active', 'signal_strength' => 5, 'notes' => 'Full 5G coverage'],
            ['county' => 'Nairobi', 'area' => 'Kilimani', 'coverage_type' => '5g', 'status' => 'active', 'signal_strength' => 5, 'notes' => null],
            ['county' => 'Nairobi', 'area' => 'Parklands', 'coverage_type' => 'fiber', 'status' => 'active', 'signal_strength' => 5, 'notes' => 'FTTH Available'],
            ['county' => 'Nairobi', 'area' => 'South B & C', 'coverage_type' => '4g', 'status' => 'active', 'signal_strength' => 4, 'notes' => null],
            ['county' => 'Nairobi', 'area' => 'Kasarani', 'coverage_type' => '4g', 'status' => 'active', 'signal_strength' => 4, 'notes' => null],
            ['county' => 'Nairobi', 'area' => 'Thika Road', 'coverage_type' => 'fiber', 'status' => 'active', 'signal_strength' => 5, 'notes' => 'Fiber corridor'],
            ['county' => 'Nairobi', 'area' => 'Huruma', 'coverage_type' => '4g', 'status' => 'limited', 'signal_strength' => 3, 'notes' => 'Coverage expansion planned'],
            ['county' => 'Mombasa', 'area' => 'Nyali', 'coverage_type' => 'fiber', 'status' => 'active', 'signal_strength' => 5, 'notes' => null],
            ['county' => 'Mombasa', 'area' => 'Tudor', 'coverage_type' => '4g', 'status' => 'active', 'signal_strength' => 4, 'notes' => null],
            ['county' => 'Mombasa', 'area' => 'Old Town', 'coverage_type' => '4g', 'status' => 'active', 'signal_strength' => 4, 'notes' => null],
            ['county' => 'Kisumu', 'area' => 'Kisumu Central', 'coverage_type' => '4g', 'status' => 'active', 'signal_strength' => 4, 'notes' => null],
            ['county' => 'Kisumu', 'area' => 'Milimani', 'coverage_type' => 'fiber', 'status' => 'planned', 'signal_strength' => 0, 'notes' => 'Fiber rollout Q2 2025'],
            ['county' => 'Nakuru', 'area' => 'Nakuru Town', 'coverage_type' => '4g', 'status' => 'active', 'signal_strength' => 4, 'notes' => null],
            ['county' => 'Eldoret', 'area' => 'Eldoret Town', 'coverage_type' => '4g', 'status' => 'active', 'signal_strength' => 3, 'notes' => null],
            ['county' => 'Thika', 'area' => 'Thika Town', 'coverage_type' => 'wimax', 'status' => 'active', 'signal_strength' => 4, 'notes' => null],
            ['county' => 'Nyeri', 'area' => 'Nyeri Town', 'coverage_type' => '4g', 'status' => 'maintenance', 'signal_strength' => 2, 'notes' => 'Tower maintenance in progress'],
            ['county' => 'Garissa', 'area' => 'Garissa Town', 'coverage_type' => '4g', 'status' => 'limited', 'signal_strength' => 2, 'notes' => 'Satellite backup available'],
            ['county' => 'Nairobi', 'area' => 'Karen', 'coverage_type' => 'fiber', 'status' => 'active', 'signal_strength' => 5, 'notes' => 'Premium residential zone'],
        ];

        foreach ($zones as $zone) {
            NetworkZone::create($zone);
        }
    }
}