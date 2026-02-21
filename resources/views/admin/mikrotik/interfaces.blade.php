@extends('layouts.admin')
@section('title','Interfaces — ' . $router->name)
@section('page-title','Network Interfaces')
@section('page-subtitle', $router->name)
@section('content')
@include('admin.mikrotik._nav')

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">MAC Address</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">MTU</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">↓ RX</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">↑ TX</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($interfaces as $iface)
            <tr class="hover:bg-orange-50/20">
                <td class="px-4 py-3">
                    <p class="text-gray-800 font-mono font-bold text-xs">{{ $iface['name'] ?? '-' }}</p>
                    @if(isset($iface['comment']) && $iface['comment'])<p class="text-gray-400 text-xs">{{ $iface['comment'] }}</p>@endif
                </td>
                <td class="px-4 py-3"><span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded font-semibold capitalize">{{ $iface['type'] ?? 'ether' }}</span></td>
                <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $iface['mac-address'] ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $iface['mtu'] ?? '1500' }}</td>
                <td class="px-4 py-3 text-green-600 text-xs font-semibold">{{ \App\Services\MikrotikService::formatBytes((int)($iface['rx-byte'] ?? 0)) }}</td>
                <td class="px-4 py-3 text-blue-500 text-xs font-semibold">{{ \App\Services\MikrotikService::formatBytes((int)($iface['tx-byte'] ?? 0)) }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ (isset($iface['disabled']) && $iface['disabled'] === 'true') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ (isset($iface['disabled']) && $iface['disabled'] === 'true') ? 'Disabled' : 'Running' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">No interfaces found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- IP Addresses -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm mt-4">
    <div class="px-4 py-3 border-b border-gray-100"><h3 class="text-gray-800 font-bold text-sm">IP Addresses</h3></div>
    <table class="w-full">
        <thead style="background:#f8fafc"><tr>
            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">IP/Prefix</th>
            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Interface</th>
            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Network</th>
            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Status</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($ipAddresses as $ip)
            <tr class="hover:bg-orange-50/20">
                <td class="px-4 py-2.5 text-blue-700 font-mono text-xs font-bold">{{ $ip['address'] ?? '-' }}</td>
                <td class="px-4 py-2.5 text-gray-700 text-xs">{{ $ip['interface'] ?? '-' }}</td>
                <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">{{ $ip['network'] ?? '-' }}</td>
                <td class="px-4 py-2.5"><span class="px-2 py-0.5 rounded-full text-xs {{ (isset($ip['disabled']) && $ip['disabled'] === 'true') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">{{ (isset($ip['disabled']) && $ip['disabled'] === 'true') ? 'Disabled' : 'Active' }}</span></td>
            </tr>
            @empty
            <tr><td colspan="4" class="py-6 text-center text-gray-400 text-sm">No IP addresses</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
