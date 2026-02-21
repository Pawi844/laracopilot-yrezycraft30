@extends('layouts.app')
@section('title', 'Contact Us - MtaaKonnect Kenya')

@section('content')
<div class="hero-gradient text-white py-20 px-4">
    <div class="max-w-7xl mx-auto text-center">
        <h1 class="text-5xl font-black mb-4">Contact MtaaKonnect</h1>
        <p class="text-xl text-sky-100">We're here to help. Reach out to us any time.</p>
    </div>
</div>

<section class="py-20 px-4">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="w-12 h-12 bg-sky-100 rounded-xl flex items-center justify-center mb-4"><i class="fas fa-phone text-sky-600 text-xl"></i></div>
                <h3 class="font-bold text-gray-800 mb-1">Call Us</h3>
                <p class="text-sky-600 font-semibold">+254 700 000 000</p>
                <p class="text-gray-500 text-sm">Mon–Sun, 24 hours</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4"><i class="fas fa-envelope text-green-600 text-xl"></i></div>
                <h3 class="font-bold text-gray-800 mb-1">Email Us</h3>
                <p class="text-sky-600 font-semibold">info@mtaakonnect.co.ke</p>
                <p class="text-gray-500 text-sm">We reply within 2 hours</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4"><i class="fas fa-map-marker-alt text-purple-600 text-xl"></i></div>
                <h3 class="font-bold text-gray-800 mb-1">Visit Us</h3>
                <p class="text-gray-600 text-sm">Westlands Business Park,<br>Nairobi, Kenya</p>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <h2 class="text-2xl font-black text-gray-800 mb-6">Send Us a Message</h2>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg flex items-center space-x-2">
                    <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-500 focus:outline-none @error('name') border-red-500 @enderror" placeholder="John Kamau" required>
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-500 focus:outline-none @error('email') border-red-500 @enderror" placeholder="john@example.com" required>
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-500 focus:outline-none" placeholder="+254 700 000 000" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Subject <span class="text-red-500">*</span></label>
                        <input type="text" name="subject" value="{{ old('subject') }}" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-500 focus:outline-none @error('subject') border-red-500 @enderror" placeholder="How can we help?" required>
                        @error('subject')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
                    <textarea name="message" rows="5" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-500 focus:outline-none @error('message') border-red-500 @enderror" placeholder="Tell us how we can help you..." required>{{ old('message') }}</textarea>
                    @error('message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="bg-sky-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-sky-700 transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-paper-plane mr-2"></i>Send Message
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
