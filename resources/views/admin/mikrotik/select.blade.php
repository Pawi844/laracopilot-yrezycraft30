@extends('layouts.admin')
@section('title','MikroTik Panel')
@section('page-title','MikroTik Live Panel')
@section('page-subtitle','Connect and manage your MikroTik routers in real-time')
@section('content')
<div class="flex justify-between items-center mb-5">
    <p class="text-gray-500 text-sm">Select a router to open the live management interface.</p>
    <div class="flex space-x-3">
        <a href="{{ route('admin.mikrotik.setup') }}" class="flex items-center space-x-2 bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-blue-800">
            <i class="fas fa-book"></i><span>Setup Guide</span>
        </a>
        <a href="{{ route('admin.routers.create') }}" class="flex items-center space-x-2 text-white px-4 py-2 rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
            <i class="fas fa-plus"></i><span>Add Router</span>
        </a>
    </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($routers as $router)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-all">
        <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#1e3a5f,#0f2744)">
                <i class="fas fa-router text-white text-lg"></i>
            </div>
            <span class="text-xs px-2 py-1 rounded-full font-semibold {{ $router->status === 'online' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                <span class="w-1.5 h-1.5 rounded-full inline-block mr-1 {{ $router->status === 'online' ? 'bg-green-500 animate-pulse' : 'bg-red-400' }}"></span>
                {{ ucfirst($router->status) }}
            </span>
        </div>
        <h3 class="text-gray-800 font-bold mb-1">{{ $router->name }}</h3>
        <p class="text-blue-600 font-mono text-sm">{{ $router->ip_address }}:{{ $router->api_port }}</p>
        <p class="text-gray-400 text-xs mt-1">{{ $router->model ?? 'MikroTik Router' }}</p>
        @if($router->last_sync)<p class="text-gray-400 text-xs mt-1"><i class="fas fa-sync-alt mr-1"></i>{{ $router->last_sync->diffForHumans() }}</p>@endif
        <a href="{{ route('admin.mikrotik.dashboard', $router->id) }}" class="mt-4 w-full text-center block text-white py-2.5 rounded-xl text-sm font-bold hover:opacity-90 transition-opacity" style="background:linear-gradient(90deg,#f97316,#ea580c)">
            <i class="fas fa-plug mr-2"></i>Connect & Manage
        </a>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
        <i class="fas fa-router text-6xl text-gray-200 mb-4 block"></i>
        <h3 class="text-gray-600 font-bold text-lg mb-2">No Routers Configured</h3>
        <p class="text-gray-400 mb-2">Add a MikroTik router to start managing it.</p>
        <p class="text-gray-400 text-sm mb-5">Not sure how to set up MikroTik? <a href="{{ route('admin.mikrotik.setup') }}" class="text-blue-600 font-semibold hover:underline">Read the Setup Guide →</a></p>
        <a href="{{ route('admin.routers.create') }}" class="text-white px-6 py-2.5 rounded-xl font-semibold inline-block" style="background:linear-gradient(90deg,#f97316,#ea580c)">
            <i class="fas fa-plus mr-1"></i>Add Router
        </a>
    </div>
    @endforelse
</div>
@endsection
