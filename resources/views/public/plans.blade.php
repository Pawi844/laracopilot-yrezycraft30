@extends('layouts.app')
@section('title', 'Plans & Pricing - MtaaKonnect Kenya')

@section('content')
<div class="hero-gradient text-white py-20 px-4">
    <div class="max-w-7xl mx-auto text-center">
        <h1 class="text-5xl font-black mb-4">Plans & Pricing</h1>
        <p class="text-xl text-sky-100">MtaaKonnect offers transparent pricing with no hidden fees. Pay via M-Pesa, bank transfer, or card.</p>
    </div>
</div>

<section class="py-20 px-4">
    <div class="max-w-7xl mx-auto">
        @php $grouped = $plans->groupBy('service.name'); @endphp
        @foreach($grouped as $serviceName => $servicePlans)
        <div class="mb-16">
            <h2 class="text-3xl font-black text-gray-800 mb-8">{{ $serviceName ?? 'Internet Plans' }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($servicePlans as $plan)
                <div class="bg-white rounded-2xl shadow-sm border {{ $plan->featured ? 'border-sky-500 shadow-sky-100 shadow-md' : 'border-gray-200' }} p-6 relative hover:shadow-md transition-all">
                    @if($plan->featured)
                    <div class="absolute -top-3 right-4">
                        <span class="bg-sky-600 text-white text-xs font-bold px-3 py-1 rounded-full">⭐ Featured</span>
                    </div>
                    @endif
                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $plan->name }}</h3>
                    <div class="mb-3">
                        <span class="text-3xl font-black text-gray-900">KES {{ number_format($plan->price) }}</span>
                        <span class="text-gray-500 text-sm">/{{ $plan->billing_cycle }}</span>
                    </div>
                    @if($plan->speed || $plan->data_limit)
                    <div class="flex flex-wrap gap-1 mb-4">
                        @if($plan->speed)<span class="bg-blue-50 text-blue-700 text-xs font-bold px-2 py-1 rounded">{{ $plan->speed }}</span>@endif
                        @if($plan->data_limit)<span class="bg-green-50 text-green-700 text-xs font-bold px-2 py-1 rounded">{{ $plan->data_limit }}</span>@endif
                    </div>
                    @endif
                    <ul class="space-y-2 mb-6">
                        @foreach($plan->feature_list as $feature)
                        <li class="flex items-center text-sm text-gray-600"><i class="fas fa-check text-sky-500 mr-2 flex-shrink-0"></i>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('contact') }}" class="block text-center {{ $plan->featured ? 'bg-sky-600 text-white hover:bg-sky-700' : 'border-2 border-sky-600 text-sky-600 hover:bg-sky-50' }} py-2.5 rounded-xl font-bold text-sm transition-all">
                        Get This Plan
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</section>
@endsection
