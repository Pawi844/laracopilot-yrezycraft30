@extends('layouts.app')
@section('title', 'About Us - MtaaKonnect Kenya')

@section('content')
<div class="hero-gradient text-white py-20 px-4">
    <div class="max-w-7xl mx-auto text-center">
        <h1 class="text-5xl font-black mb-4">About MtaaKonnect</h1>
        <p class="text-xl text-sky-100 max-w-3xl mx-auto">Connecting Kenya's neighbourhoods, one mtaa at a time since 2015.</p>
    </div>
</div>

<section class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <div>
            <span class="text-sky-600 font-bold uppercase tracking-widest text-sm">Our Story</span>
            <h2 class="text-4xl font-black text-gray-900 mt-2 mb-6">Born in Kenya, Built for Kenya</h2>
            <p class="text-gray-600 leading-relaxed mb-4">MtaaKonnect was founded in Nairobi in 2015 with a bold mission: to bring affordable, high-speed internet to every neighbourhood (mtaa) in Kenya. We started as a small team of 10 engineers and entrepreneurs who believed that connectivity is a right, not a privilege.</p>
            <p class="text-gray-600 leading-relaxed mb-4">Today, we serve over 50,000 customers across all 47 counties, operating one of the most extensive fiber and 4G/5G networks in the country. We are fully licensed by the Communications Authority of Kenya and committed to expanding our infrastructure every year.</p>
            <p class="text-gray-600 leading-relaxed">Our M-Pesa payment integration, Swahili-speaking support team, and locally tailored plans make MtaaKonnect the preferred choice for Kenyan homes and businesses.</p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-sky-600 text-white rounded-2xl p-8 text-center"><p class="text-5xl font-black">2015</p><p class="text-sky-200 mt-1">Year Founded</p></div>
            <div class="bg-gray-900 text-white rounded-2xl p-8 text-center"><p class="text-5xl font-black">50K+</p><p class="text-gray-400 mt-1">Customers</p></div>
            <div class="bg-green-600 text-white rounded-2xl p-8 text-center"><p class="text-5xl font-black">47</p><p class="text-green-200 mt-1">Counties</p></div>
            <div class="bg-sky-100 rounded-2xl p-8 text-center"><p class="text-5xl font-black text-sky-700">500+</p><p class="text-sky-600 mt-1">Team Members</p></div>
        </div>
    </div>
</section>

<section class="py-20 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-black text-gray-900">Our Leadership Team</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm">
                <div class="w-20 h-20 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">👨‍💼</div>
                <h3 class="text-xl font-bold text-gray-800">Peter Odhiambo</h3>
                <p class="text-sky-600 font-medium text-sm">Chief Executive Officer</p>
                <p class="text-gray-500 text-sm mt-3">20 years in telecoms across East Africa. Former Safaricom Director of Infrastructure.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">👩‍💼</div>
                <h3 class="text-xl font-bold text-gray-800">Sarah Wanjiru</h3>
                <p class="text-sky-600 font-medium text-sm">Chief Operations Officer</p>
                <p class="text-gray-500 text-sm mt-3">Expert in network operations and customer experience across East Africa's telecom sector.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm">
                <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">👨‍💻</div>
                <h3 class="text-xl font-bold text-gray-800">Brian Kariuki</h3>
                <p class="text-sky-600 font-medium text-sm">Chief Technology Officer</p>
                <p class="text-gray-500 text-sm mt-3">Led the rollout of Kenya's largest private fiber network spanning 5,000+ km of cable.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-16 px-4 hero-gradient text-white">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl font-black mb-4">Want to Join Our Team?</h2>
        <p class="text-sky-100 text-lg mb-8">We're always looking for talented individuals who are passionate about connectivity and technology.</p>
        <a href="{{ route('contact') }}" class="bg-white text-sky-700 px-8 py-4 rounded-full font-bold hover:bg-sky-50 transition-all">Get in Touch</a>
    </div>
</section>
@endsection
