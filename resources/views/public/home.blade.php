@extends('layouts.app')
@section('title', 'MtaaKonnect - Fast & Reliable Internet in Kenya')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient text-white py-24 px-4">
    <div class="max-w-7xl mx-auto text-center">
        <div class="inline-flex items-center bg-white/10 text-white text-sm px-4 py-2 rounded-full mb-6 backdrop-blur-sm">
            <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span> Kenya's Trusted Connectivity Partner
        </div>
        <h1 class="text-5xl md:text-7xl font-black mb-6 leading-tight">
            Connect Your<br>
            <span class="text-sky-300">Mtaa Today</span>
        </h1>
        <p class="text-xl md:text-2xl text-sky-100 mb-8 max-w-3xl mx-auto leading-relaxed">
            Ultra-fast fiber internet, 4G/5G mobile data, and business solutions designed for Kenya's digital economy. MtaaKonnect — bringing fast internet to every neighbourhood.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('plans') }}" class="bg-white text-sky-700 px-8 py-4 rounded-full text-lg font-bold hover:bg-sky-50 transition-all duration-300 shadow-lg hover:shadow-xl">
                <i class="fas fa-rocket mr-2"></i>View Our Plans
            </a>
            <a href="{{ route('contact') }}" class="border-2 border-white text-white px-8 py-4 rounded-full text-lg font-bold hover:bg-white/10 transition-all duration-300">
                <i class="fas fa-phone mr-2"></i>Talk to Us
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-16 max-w-4xl mx-auto">
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6">
                <p class="text-4xl font-black text-white">50K+</p>
                <p class="text-sky-200 text-sm mt-1">Happy Customers</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6">
                <p class="text-4xl font-black text-white">47</p>
                <p class="text-sky-200 text-sm mt-1">Counties Covered</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6">
                <p class="text-4xl font-black text-white">99.9%</p>
                <p class="text-sky-200 text-sm mt-1">Network Uptime</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6">
                <p class="text-4xl font-black text-white">24/7</p>
                <p class="text-sky-200 text-sm mt-1">Customer Support</p>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <span class="text-sky-600 font-bold uppercase tracking-widest text-sm">What We Offer</span>
            <h2 class="text-4xl font-black text-gray-900 mt-2">Our Services</h2>
            <p class="text-gray-500 mt-4 text-lg max-w-2xl mx-auto">From home internet to enterprise solutions, MtaaKonnect has the right connectivity package for every need.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($services as $service)
            <div class="group bg-white border border-gray-200 rounded-2xl p-8 hover:shadow-xl hover:border-sky-200 transition-all duration-300">
                <div class="text-5xl mb-4">{{ $service->icon }}</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $service->name }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $service->short_description }}</p>
                <a href="{{ route('services') }}" class="mt-4 inline-flex items-center text-sky-600 font-semibold text-sm group-hover:text-sky-700">
                    Learn More <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Plans -->
@if($plans->count())
<section class="py-20 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <span class="text-sky-600 font-bold uppercase tracking-widest text-sm">Pricing</span>
            <h2 class="text-4xl font-black text-gray-900 mt-2">Popular Plans</h2>
            <p class="text-gray-500 mt-4 text-lg">Simple, transparent pricing. No hidden fees.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($plans as $index => $plan)
            <div class="bg-white rounded-2xl shadow-sm border {{ $index === 1 ? 'border-sky-500 shadow-sky-100 shadow-lg scale-105' : 'border-gray-200' }} p-8 relative">
                @if($index === 1)
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    <span class="bg-sky-600 text-white text-xs font-bold px-4 py-1.5 rounded-full">MOST POPULAR</span>
                </div>
                @endif
                <div class="mb-6">
                    <p class="text-gray-500 text-sm">{{ $plan->service->name ?? 'Internet Plan' }}</p>
                    <h3 class="text-xl font-bold text-gray-800 mt-1">{{ $plan->name }}</h3>
                    <div class="mt-4">
                        <span class="text-4xl font-black text-gray-900">KES {{ number_format($plan->price) }}</span>
                        <span class="text-gray-500 text-sm">/{{ $plan->billing_cycle }}</span>
                    </div>
                    @if($plan->speed)
                    <div class="mt-2 flex items-center space-x-2">
                        <span class="bg-sky-50 text-sky-700 text-xs font-bold px-3 py-1 rounded-full">{{ $plan->speed }}</span>
                        <span class="bg-green-50 text-green-700 text-xs font-bold px-3 py-1 rounded-full">{{ $plan->data_limit }}</span>
                    </div>
                    @endif
                </div>
                <ul class="space-y-3 mb-8">
                    @foreach($plan->feature_list as $feature)
                    <li class="flex items-center space-x-3 text-sm">
                        <i class="fas fa-check text-sky-500 flex-shrink-0"></i>
                        <span class="text-gray-700">{{ $feature }}</span>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('contact') }}" class="block text-center {{ $index === 1 ? 'bg-sky-600 hover:bg-sky-700 text-white' : 'border-2 border-sky-600 text-sky-600 hover:bg-sky-50' }} py-3 rounded-xl font-bold transition-all duration-300">
                    Get Started
                </a>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-10">
            <a href="{{ route('plans') }}" class="text-sky-600 font-semibold hover:underline">View all plans →</a>
        </div>
    </div>
</section>
@endif

<!-- Why Choose Us -->
<section class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="text-sky-600 font-bold uppercase tracking-widest text-sm">Why MtaaKonnect</span>
                <h2 class="text-4xl font-black text-gray-900 mt-2 mb-6">Built for Kenya's Digital Future</h2>
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-sky-100 rounded-xl flex items-center justify-center flex-shrink-0"><i class="fas fa-bolt text-sky-600"></i></div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-1">Blazing Fast Speeds</h3>
                            <p class="text-gray-500 text-sm">Our fiber and 5G network delivers speeds up to 1 Gbps, so you never have to wait.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0"><i class="fas fa-shield-alt text-green-600"></i></div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-1">99.9% Network Reliability</h3>
                            <p class="text-gray-500 text-sm">Our redundant network infrastructure ensures you stay connected even during outages.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0"><i class="fas fa-headset text-purple-600"></i></div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-1">24/7 Local Support</h3>
                            <p class="text-gray-500 text-sm">Our Kenyan-based support team is always ready to help you in Swahili or English.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0"><i class="fas fa-mobile-alt text-orange-600"></i></div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-1">M-Pesa Payments</h3>
                            <p class="text-gray-500 text-sm">Pay easily with M-Pesa, bank transfer, or card. Simple and convenient.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-sky-50 to-blue-100 rounded-3xl p-8">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-2xl p-6 shadow-sm"><i class="fas fa-wifi text-sky-500 text-2xl mb-3"></i><p class="text-2xl font-black text-gray-800">100 Mbps</p><p class="text-sm text-gray-500">Max fiber speed</p></div>
                    <div class="bg-sky-600 rounded-2xl p-6 shadow-sm"><i class="fas fa-map-marked-alt text-white text-2xl mb-3"></i><p class="text-2xl font-black text-white">47</p><p class="text-sm text-sky-200">Counties served</p></div>
                    <div class="bg-gray-900 rounded-2xl p-6 shadow-sm"><i class="fas fa-clock text-yellow-400 text-2xl mb-3"></i><p class="text-2xl font-black text-white">4hrs</p><p class="text-sm text-gray-400">Avg installation</p></div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm"><i class="fas fa-star text-yellow-500 text-2xl mb-3"></i><p class="text-2xl font-black text-gray-800">4.8/5</p><p class="text-sm text-gray-500">Customer rating</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-20 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <span class="text-sky-600 font-bold uppercase tracking-widest text-sm">Testimonials</span>
            <h2 class="text-4xl font-black text-gray-900 mt-2">What Our Customers Say</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <div class="flex text-yellow-400 mb-4">★★★★★</div>
                <p class="text-gray-600 leading-relaxed">"MtaaKonnect transformed my work from home experience. The fiber connection is incredibly stable, and I've never had a dropout during video calls. Best decision I made!"</p>
                <div class="flex items-center space-x-3 mt-6">
                    <div class="w-10 h-10 bg-sky-100 rounded-full flex items-center justify-center font-bold text-sky-700">J</div>
                    <div><p class="font-bold text-gray-800 text-sm">James Mwangi</p><p class="text-gray-500 text-xs">Nairobi · Home Fiber Customer</p></div>
                </div>
            </div>
            <div class="bg-sky-600 rounded-2xl p-8 shadow-lg">
                <div class="flex text-yellow-400 mb-4">★★★★★</div>
                <p class="text-white leading-relaxed">"Our business operations run smoothly thanks to MtaaKonnect's enterprise internet. The dedicated support line has saved us countless times. Highly recommended!"</p>
                <div class="flex items-center space-x-3 mt-6">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center font-bold text-white">G</div>
                    <div><p class="font-bold text-white text-sm">Grace Achieng</p><p class="text-sky-200 text-xs">Mombasa · Business Internet</p></div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <div class="flex text-yellow-400 mb-4">★★★★★</div>
                <p class="text-gray-600 leading-relaxed">"I switched from another provider and the difference is night and day. No throttling, no excuses — just fast internet every single day. Asante MtaaKonnect!"</p>
                <div class="flex items-center space-x-3 mt-6">
                    <div class="w-10 h-10 bg-sky-100 rounded-full flex items-center justify-center font-bold text-sky-700">D</div>
                    <div><p class="font-bold text-gray-800 text-sm">David Kimani</p><p class="text-gray-500 text-xs">Nakuru · Fiber Premium Plan</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Banner -->
<section class="py-20 px-4 hero-gradient text-white">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-4xl font-black mb-4">Ready to Get Connected?</h2>
        <p class="text-sky-100 text-xl mb-8">Join over 50,000 happy Kenyans who trust MtaaKonnect for their internet needs.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('plans') }}" class="bg-white text-sky-700 px-8 py-4 rounded-full font-bold hover:bg-sky-50 transition-all">Choose a Plan</a>
            <a href="{{ route('contact') }}" class="border-2 border-white text-white px-8 py-4 rounded-full font-bold hover:bg-white/10 transition-all">Contact Sales</a>
        </div>
    </div>
</section>
@endsection
