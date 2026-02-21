@extends('layouts.app')
@section('title', 'Network Coverage - MtaaKonnect Kenya')

@section('content')
<div class="hero-gradient text-white py-20 px-4">
    <div class="max-w-7xl mx-auto text-center">
        <h1 class="text-5xl font-black mb-4">MtaaKonnect Coverage</h1>
        <p class="text-xl text-sky-100">Expanding across all 47 counties of Kenya. Check if we're in your mtaa.</p>
    </div>
</div>

<section class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
                <div class="w-4 h-4 bg-green-500 rounded-full mx-auto mb-2"></div>
                <p class="font-bold text-green-800 text-sm">Active</p>
                <p class="text-green-600 text-xs">Full coverage available</p>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                <div class="w-4 h-4 bg-blue-500 rounded-full mx-auto mb-2"></div>
                <p class="font-bold text-blue-800 text-sm">Planned</p>
                <p class="text-blue-600 text-xs">Coming soon</p>
            </div>
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-center">
                <div class="w-4 h-4 bg-orange-500 rounded-full mx-auto mb-2"></div>
                <p class="font-bold text-orange-800 text-sm">Maintenance</p>
                <p class="text-orange-600 text-xs">Temporary disruption</p>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center">
                <div class="w-4 h-4 bg-yellow-500 rounded-full mx-auto mb-2"></div>
                <p class="font-bold text-yellow-800 text-sm">Limited</p>
                <p class="text-yellow-600 text-xs">Partial coverage</p>
            </div>
        </div>

        <div class="bg-sky-50 rounded-2xl p-8 text-center mb-12">
            <i class="fas fa-map text-sky-400 text-6xl mb-4"></i>
            <h2 class="text-2xl font-black text-gray-800 mb-2">Interactive Coverage Map</h2>
            <p class="text-gray-500 mb-6">Contact us to check real-time coverage availability in your specific location.</p>
            <a href="{{ route('contact') }}" class="bg-sky-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-sky-700 transition-all inline-block">
                <i class="fas fa-search-location mr-2"></i>Check My Area
            </a>
        </div>

        <h2 class="text-3xl font-black text-gray-800 mb-6">Covered Counties</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach(['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika', 'Malindi', 'Kitale', 'Garissa', 'Nyeri', 'Machakos', 'Meru', 'Embu', 'Kakamega', 'Kisii', 'Kilifi', 'Lamu', 'Voi', 'Isiolo', 'Marsabit', 'Wajir', 'Mandera', 'Turkana', 'Samburu'] as $county)
            <div class="bg-white border border-gray-200 rounded-lg p-3 text-center hover:border-sky-300 hover:bg-sky-50 transition-all">
                <i class="fas fa-signal text-sky-500 text-sm mb-1"></i>
                <p class="text-sm font-semibold text-gray-700">{{ $county }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-16 px-4 hero-gradient text-white">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl font-black mb-4">Don't See Your Area?</h2>
        <p class="text-sky-100 text-lg mb-8">We're expanding rapidly. Register your interest and we'll notify you when MtaaKonnect reaches your neighbourhood.</p>
        <a href="{{ route('contact') }}" class="bg-white text-sky-700 px-8 py-4 rounded-full font-bold hover:bg-sky-50 transition-all">Register Interest</a>
    </div>
</section>
@endsection
