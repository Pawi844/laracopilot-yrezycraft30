<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Service;

class PlanSeeder extends Seeder
{
    public function run()
    {
        $fiberService = Service::where('slug', 'home-fiber')->first();
        $mobileService = Service::where('slug', 'mobile-data')->first();
        $businessService = Service::where('slug', 'business-internet')->first();

        if ($fiberService) {
            $plans = [
                ['name' => 'Fiber Starter', 'service_id' => $fiberService->id, 'price' => 2500, 'billing_cycle' => 'monthly', 'speed' => '10 Mbps', 'data_limit' => 'Unlimited', 'description' => 'Perfect for light internet users and small households.', 'features' => "10 Mbps Download\n5 Mbps Upload\nUnlimited Data\nFree Router\n24/7 Support", 'active' => true, 'featured' => false],
                ['name' => 'Fiber Home', 'service_id' => $fiberService->id, 'price' => 3500, 'billing_cycle' => 'monthly', 'speed' => '25 Mbps', 'data_limit' => 'Unlimited', 'description' => 'Ideal for families who love streaming and gaming.', 'features' => "25 Mbps Download\n10 Mbps Upload\nUnlimited Data\nFree Router\nPriority Support\nFree Installation", 'active' => true, 'featured' => true],
                ['name' => 'Fiber Premium', 'service_id' => $fiberService->id, 'price' => 5500, 'billing_cycle' => 'monthly', 'speed' => '50 Mbps', 'data_limit' => 'Unlimited', 'description' => 'For power users who demand the best speeds.', 'features' => "50 Mbps Download\n25 Mbps Upload\nUnlimited Data\nPremium Router\nDedicated Support\nFree Installation\nStatic IP", 'active' => true, 'featured' => true],
                ['name' => 'Fiber Ultra', 'service_id' => $fiberService->id, 'price' => 8500, 'billing_cycle' => 'monthly', 'speed' => '100 Mbps', 'data_limit' => 'Unlimited', 'description' => 'Maximum speed for the most demanding households.', 'features' => "100 Mbps Download\n50 Mbps Upload\nUnlimited Data\nPremium Router\nVIP Support\nFree Installation\nStatic IP\nMonthly Health Check", 'active' => true, 'featured' => true],
            ];

            foreach ($plans as $plan) {
                Plan::create($plan);
            }
        }

        if ($mobileService) {
            $plans = [
                ['name' => 'Daily Data 500MB', 'service_id' => $mobileService->id, 'price' => 50, 'billing_cycle' => 'daily', 'speed' => '4G LTE', 'data_limit' => '500 MB', 'description' => 'Quick daily bundle for light browsing.', 'features' => "500 MB Data\n4G LTE Speed\nValid for 24 Hours", 'active' => true, 'featured' => false],
                ['name' => 'Weekly 5GB', 'service_id' => $mobileService->id, 'price' => 300, 'billing_cycle' => 'weekly', 'speed' => '4G LTE', 'data_limit' => '5 GB', 'description' => 'Weekly bundle perfect for moderate use.', 'features' => "5 GB Data\n4G LTE Speed\nValid for 7 Days\nRollover Data", 'active' => true, 'featured' => false],
                ['name' => 'Monthly 20GB', 'service_id' => $mobileService->id, 'price' => 1000, 'billing_cycle' => 'monthly', 'speed' => '4G LTE', 'data_limit' => '20 GB', 'description' => 'Monthly bundle for regular internet users.', 'features' => "20 GB Data\n4G LTE Speed\nValid for 30 Days\nRollover Data\nFree WhatsApp", 'active' => true, 'featured' => false],
                ['name' => 'Monthly Unlimited 5G', 'service_id' => $mobileService->id, 'price' => 2000, 'billing_cycle' => 'monthly', 'speed' => '5G', 'data_limit' => 'Unlimited', 'description' => 'Unlimited 5G data for the heaviest users.', 'features' => "Unlimited Data\n5G Speed\nValid for 30 Days\nNo Throttling\nFree Calls", 'active' => true, 'featured' => false],
            ];

            foreach ($plans as $plan) {
                Plan::create($plan);
            }
        }

        if ($businessService) {
            $plans = [
                ['name' => 'Business Basic', 'service_id' => $businessService->id, 'price' => 8000, 'billing_cycle' => 'monthly', 'speed' => '20 Mbps', 'data_limit' => 'Unlimited', 'description' => 'Entry-level business internet for small offices.', 'features' => "20 Mbps Dedicated\nUnlimited Data\nStatic IP\nSLA 99.5%\nBusiness Support", 'active' => true, 'featured' => false],
                ['name' => 'Business Pro', 'service_id' => $businessService->id, 'price' => 15000, 'billing_cycle' => 'monthly', 'speed' => '50 Mbps', 'data_limit' => 'Unlimited', 'description' => 'Professional grade internet for growing businesses.', 'features' => "50 Mbps Dedicated\nUnlimited Data\n5 Static IPs\nSLA 99.9%\n24/7 Priority Support\nMonthly Report", 'active' => true, 'featured' => true],
                ['name' => 'Enterprise', 'service_id' => $businessService->id, 'price' => 35000, 'billing_cycle' => 'monthly', 'speed' => '200 Mbps', 'data_limit' => 'Unlimited', 'description' => 'Enterprise-grade connectivity for large organizations.', 'features' => "200 Mbps Dedicated\nUnlimited Data\n10 Static IPs\nSLA 99.99%\nDedicated Account Manager\nNetwork Monitoring\nRedundant Links", 'active' => true, 'featured' => false],
            ];

            foreach ($plans as $plan) {
                Plan::create($plan);
            }
        }
    }
}