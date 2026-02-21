<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $services = [
            ['name' => 'Home Fiber Internet', 'slug' => 'home-fiber', 'short_description' => 'Ultra-fast fiber internet for your home with unlimited data and no throttling.', 'description' => 'Experience blazing-fast fiber internet designed for modern households. Stream 4K content, video conference, game online, and connect all your smart devices simultaneously with our rock-solid fiber network.', 'icon' => '🏠', 'category' => 'internet', 'active' => true],
            ['name' => 'Business Internet', 'slug' => 'business-internet', 'short_description' => 'Reliable, high-speed internet solutions tailored for businesses of all sizes.', 'description' => 'Power your business operations with our enterprise-grade internet solutions. We offer dedicated bandwidth, SLA guarantees, static IP addresses, and 24/7 priority support to keep your business running smoothly.', 'icon' => '🏢', 'category' => 'business', 'active' => true],
            ['name' => '4G/5G Mobile Data', 'slug' => 'mobile-data', 'short_description' => 'High-speed mobile internet with nationwide 4G and 5G coverage across Kenya.', 'description' => 'Stay connected on the go with our extensive 4G LTE and 5G network. Choose from daily, weekly, or monthly data bundles that fit your lifestyle and budget.', 'icon' => '📱', 'category' => 'mobile', 'active' => true],
            ['name' => 'TV Streaming', 'slug' => 'tv-streaming', 'short_description' => 'Premium TV packages with local and international channels plus on-demand content.', 'description' => 'Enjoy a world of entertainment with our TV streaming service. Access hundreds of local and international channels, plus an extensive on-demand library of movies, series, and sports.', 'icon' => '📺', 'category' => 'tv', 'active' => true],
            ['name' => 'VoIP Solutions', 'slug' => 'voip', 'short_description' => 'Cost-effective voice calling solutions for businesses using internet technology.', 'description' => 'Reduce your communication costs with our VoIP solutions. Make crystal-clear calls locally and internationally at a fraction of the cost of traditional telephony.', 'icon' => '📞', 'category' => 'voip', 'active' => true],
            ['name' => 'Cloud & Hosting', 'slug' => 'cloud-hosting', 'short_description' => 'Scalable cloud infrastructure and web hosting services for businesses.', 'description' => 'Host your websites, applications, and data on our secure cloud infrastructure. We provide scalable solutions with 99.9% uptime guarantee and full technical support.', 'icon' => '☁️', 'category' => 'business', 'active' => true],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}