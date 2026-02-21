@extends('layouts.admin')
@section('title','MikroTik — ' . $router->name)
@section('page-title','MikroTik: ' . $router->name)
@section('page-subtitle', $router->ip_address . ' · Live Router Interface')
@section('content')
@include('admin.mikrotik._nav')

@if(!$connected)
<div class="bg-red-50 border border-red-300 rounded-xl p-6 text-center">
    <i class="fas fa-exclamation-triangle text-red-400 text-4xl mb-3 block"></i>
    <h2 class="text-red-700 font-bold text-lg">Cannot Connect to Router</h2>
    <p class="text-red-500 mt-1">Unable to reach {{ $router->ip_address }}:{{ $router->api_port }}. Check IP, API port (8728), credentials, and ensure the API service is enabled on MikroTik (<code>ip service set api disabled=no</code>).</p>
    <a href="{{ route('admin.routers.edit', $router->id) }}" class="mt-4 inline-block bg-red-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-red-700">Edit Router Settings</a>
</div>
@else

<!-- Resource KPIs -->
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 mb-4">
    <div class="bg-white rounded-xl border border-gray-100 p-3 shadow-sm">
        <p class="text-gray-400 text-xs mb-1">Router Name</p>
        <p class="text-gray-800 font-bold text-sm">{{ $identity['name'] ?? $router->name }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-3 shadow-sm">
        <p class="text-gray-400 text-xs mb-1">Platform</p>
        <p class="text-gray-800 font-bold text-sm">{{ $resources['platform'] ?? 'MikroTik' }}</p>
        <p class="text-gray-400 text-xs">{{ $resources['version'] ?? '' }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-3 shadow-sm">
        <p class="text-gray-400 text-xs mb-1">Uptime</p>
        <p class="text-green-600 font-bold text-sm">{{ $resources['uptime'] ?? 'N/A' }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-3 shadow-sm">
        <p class="text-gray-400 text-xs mb-1">CPU Load</p>
        <p class="font-bold text-sm {{ ($resources['cpu-load'] ?? 0) > 80 ? 'text-red-600' : 'text-blue-700' }}">{{ $resources['cpu-load'] ?? '0' }}%</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-3 shadow-sm">
        <p class="text-gray-400 text-xs mb-1">Memory</p>
        @php
            $totalMem = $resources['total-memory'] ?? 0;
            $freeMem  = $resources['free-memory'] ?? 0;
            $usedMem  = $totalMem - $freeMem;
            $memPct   = $totalMem > 0 ? round($usedMem/$totalMem*100) : 0;
        @endphp
        <p class="font-bold text-sm {{ $memPct > 80 ? 'text-red-600' : 'text-purple-700' }}">{{ $memPct }}%</p>
        <p class="text-gray-400 text-xs">{{ \App\Services\MikrotikService::formatBytes($usedMem) }} / {{ \App\Services\MikrotikService::formatBytes($totalMem) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-3 shadow-sm">
        <p class="text-gray-400 text-xs mb-1">HDD Free</p>
        <p class="font-bold text-sm text-orange-600">{{ \App\Services\MikrotikService::formatBytes($resources['free-hdd-space'] ?? 0) }}</p>
        <p class="text-gray-400 text-xs">of {{ \App\Services\MikrotikService::formatBytes($resources['total-hdd-space'] ?? 0) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
    <!-- Active PPPoE -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                <h3 class="text-gray-800 font-bold text-sm">PPPoE Active</h3>
                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full font-bold">{{ count($pppoeActive) }}</span>
            </div>
            <a href="{{ route('admin.mikrotik.pppoe', $router->id) }}" class="text-orange-500 text-xs hover:underline">View All →</a>
        </div>
        <div class="overflow-y-auto" style="max-height:220px">
            @forelse(array_slice($pppoeActive,0,8) as $conn)
            <div class="px-4 py-2.5 border-b border-gray-50 flex justify-between items-center hover:bg-orange-50/30">
                <div>
                    <p class="text-blue-700 font-mono text-xs font-bold">{{ $conn['name'] ?? '-' }}</p>
                    <p class="text-gray-400 text-xs">{{ $conn['address'] ?? '-' }} · {{ $conn['service'] ?? 'pppoe' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-green-600 text-xs">↓{{ $conn['rx'] ?? '0' }}</p>
                    <p class="text-blue-500 text-xs">↑{{ $conn['tx'] ?? '0' }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-xs text-center py-6">No active PPPoE connections</p>
            @endforelse
        </div>
    </div>

    <!-- Active Hotspot -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                <h3 class="text-gray-800 font-bold text-sm">Hotspot Active</h3>
                <span class="bg-orange-100 text-orange-700 text-xs px-2 py-0.5 rounded-full font-bold">{{ count($hotspotActive) }}</span>
            </div>
            <a href="{{ route('admin.mikrotik.hotspot', $router->id) }}" class="text-orange-500 text-xs hover:underline">View All →</a>
        </div>
        <div class="overflow-y-auto" style="max-height:220px">
            @forelse(array_slice($hotspotActive,0,8) as $conn)
            <div class="px-4 py-2.5 border-b border-gray-50 flex justify-between items-center hover:bg-orange-50/30">
                <div>
                    <p class="text-orange-700 font-mono text-xs font-bold">{{ $conn['user'] ?? '-' }}</p>
                    <p class="text-gray-400 text-xs">{{ $conn['address'] ?? '-' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-500 text-xs">{{ $conn['uptime'] ?? '-' }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-xs text-center py-6">No active hotspot users</p>
            @endforelse
        </div>
    </div>

    <!-- Interfaces -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-gray-800 font-bold text-sm">Interfaces</h3>
            <a href="{{ route('admin.mikrotik.interfaces', $router->id) }}" class="text-orange-500 text-xs hover:underline">View All →</a>
        </div>
        <div class="overflow-y-auto" style="max-height:220px">
            @forelse($interfaces as $iface)
            <div class="px-4 py-2.5 border-b border-gray-50 flex justify-between items-center hover:bg-orange-50/30">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full {{ isset($iface['disabled']) && $iface['disabled'] === 'true' ? 'bg-gray-300' : 'bg-green-500' }}"></span>
                    <div>
                        <p class="text-gray-800 font-mono text-xs font-semibold">{{ $iface['name'] ?? '-' }}</p>
                        <p class="text-gray-400 text-xs capitalize">{{ $iface['type'] ?? 'ether' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-green-600 text-xs">↓{{ \App\Services\MikrotikService::formatBytes((int)($iface['rx-byte'] ?? 0)) }}</p>
                    <p class="text-blue-500 text-xs">↑{{ \App\Services\MikrotikService::formatBytes((int)($iface['tx-byte'] ?? 0)) }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-xs text-center py-6">No interfaces found</p>
            @endforelse
        </div>
    </div>
</div>

<!-- IP Addresses -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center">
        <h3 class="text-gray-800 font-bold text-sm">IP Addresses</h3>
        <form action="{{ route('admin.mikrotik.sync', $router->id) }}" method="POST" class="inline">
            @csrf
            <button class="text-white px-3 py-1.5 rounded-lg text-xs font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
                <i class="fas fa-sync-alt mr-1"></i>Sync RADIUS Users
            </button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead style="background:#f8fafc">
                <tr>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">IP Address</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Interface</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Network</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($ipAddresses as $ip)
                <tr class="hover:bg-orange-50/20">
                    <td class="px-4 py-2.5 text-blue-700 font-mono text-xs font-bold">{{ $ip['address'] ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-gray-700 text-xs">{{ $ip['interface'] ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-gray-500 text-xs font-mono">{{ $ip['network'] ?? '-' }}</td>
                    <td class="px-4 py-2.5">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ isset($ip['disabled']) && $ip['disabled'] === 'true' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ isset($ip['disabled']) && $ip['disabled'] === 'true' ? 'Disabled' : 'Active' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-sm">No IP addresses found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
