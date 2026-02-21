@extends('layouts.app')
@section('title', 'Services - MtaaKonnect Kenya')

@section('content')
<div class="hero-gradient text-white py-20 px-4">
    <div class="max-w-7xl mx-auto text-center">
        <h1 class="text-5xl font-black mb-4">Our Services</h1>
        <p class="text-xl text-sky-100 max-w-2xl mx-auto">Complete connectivity solutions for homes, businesses, and enterprises across Kenya — from MtaaKonnect.</p>
    </div>
</div>

<section class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            @foreach($services as $service)
            <div class="bg-white border border-gray-200 rounded-2xl p-10 hover:shadow-xl hover:border-sky-300 transition-all duration-300 group">
                <div class="flex items-start space-x-6">
                    <div class="text-6xl">{{ $service->icon }}</div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-2xl font-black text-gray-800">{{ $service->name }}</h3>
                            <span class="bg-sky-100 text-sky-700 text-xs font-bold px-3 py-1 rounded-full capitalize">{{ $service->category }}</span>
                        </div>
                        <p class="text-gray-600 leading-relaxed mb-4">{{ $service->description }}</p>
                        <a href="{{ route('plans') }}" class="inline-flex items-center text-sky-600 font-bold hover:text-sky-700">
                            View Plans <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-16 px-4 bg-sky-50">
    <div class="max-w-5xl mx-auto text-center">
        <h2 class="text-3xl font-black text-gray-900 mb-4">Not Sure Which Service Is Right For You?</h2>
        <p class="text-gray-500 text-lg mb-8">Our MtaaKonnect experts will help you find the perfect plan for your needs and budget.</p>
        <a href="{{ route('contact') }}" class="bg-sky-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-sky-700 transition-all shadow-md hover:shadow-lg">
            <i class="fas fa-headset mr-2"></i>Talk to an Expert
        </a>
    </div>
</section>
@endsection
