@extends('layouts.admin')
@section('title','NAS Details')
@section('page-title','NAS Server Details')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-white font-bold">{{ $nas->name }}</h2>
            <span class="px-2 py-0.5 rounded-full text-xs {{ $nas->status === 'active' ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300' }}">{{ ucfirst($nas->status) }}</span>
        </div>
        <div class="space-y-3 text-sm">
            <div><p class="text-slate-500 text-xs">Short Name</p><p class="text-white font-mono">{{ $nas->shortname }}</p></div>
            <div><p class="text-slate-500 text-xs">Type</p><p class="text-slate-300 capitalize">{{ $nas->type }}</p></div>
            <div><p class="text-slate-500 text-xs">RADIUS Secret</p><p class="text-white font-mono text-xs">{{ $nas->secret }}</p></div>
            <div><p class="text-slate-500 text-xs">Community</p><p class="text-slate-300">{{ $nas->community }}</p></div>
            <div><p class="text-slate-500 text-xs">Last Seen</p><p class="text-slate-300">{{ $nas->last_seen ? $nas->last_seen->diffForHumans() : 'Never' }}</p></div>
        </div>
        <div class="mt-4">
            <p class="text-slate-500 text-xs mb-2">IP Addresses</p>
            <div class="flex flex-wrap gap-1">
                @foreach($nas->ip_addresses ?? [] as $ip)
                <span class="bg-sky-900 text-sky-300 text-xs px-2 py-1 rounded font-mono">{{ $ip }}</span>
                @endforeach
            </div>
        </div>
        <div class="mt-4 flex space-x-2">
            <a href="{{ route('admin.nas.edit', $nas->id) }}" class="flex-1 text-center bg-sky-600 text-white py-1.5 rounded-lg text-xs hover:bg-sky-700">Edit</a>
            <form action="{{ route('admin.nas.test', $nas->id) }}" method="POST" class="flex-1">
                @csrf <button class="w-full bg-green-700 text-white py-1.5 rounded-lg text-xs hover:bg-green-600">Test Conn.</button>
            </form>
        </div>
    </div>
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
            <h3 class="text-white font-bold text-sm mb-3">Connected Routers ({{ $nas->routers->count() }})</h3>
            @forelse($nas->routers as $router)
            <div class="flex items-center justify-between bg-slate-900 rounded-lg px-3 py-2 mb-2">
                <div><p class="text-white text-sm">{{ $router->name }}</p><p class="text-slate-500 text-xs font-mono">{{ $router->ip_address }}</p></div>
                <span class="text-xs px-2 py-0.5 rounded-full {{ $router->status === 'online' ? 'bg-green-900 text-green-300' : 'bg-slate-700 text-slate-400' }}">{{ ucfirst($router->status) }}</span>
            </div>
            @empty
            <p class="text-slate-500 text-sm">No routers connected to this NAS.</p>
            @endforelse
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
            <h3 class="text-white font-bold text-sm mb-3">Clients on this NAS ({{ $nas->clients->count() }})</h3>
            @forelse($nas->clients->take(10) as $client)
            <div class="flex items-center justify-between bg-slate-900 rounded-lg px-3 py-2 mb-1">
                <p class="text-white text-xs font-mono">{{ $client->username }}</p>
                <span class="text-xs px-2 py-0.5 rounded-full {{ $client->status === 'active' ? 'bg-green-900 text-green-300' : 'bg-slate-700 text-slate-400' }}">{{ ucfirst($client->status) }}</span>
            </div>
            @empty
            <p class="text-slate-500 text-sm">No clients on this NAS.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
