@extends('layouts.admin')
@section('title','DHCP/ARP — ' . $router->name)
@section('page-title','DHCP Leases & ARP Table')
@section('page-subtitle', $router->name)
@section('content')
@include('admin.mikrotik._nav')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-4">
    <div class="px-4 py-3 border-b border-gray-100"><h3 class="text-gray-800 font-bold text-sm">DHCP Leases ({{ count($leases) }})</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead style="background:#f8fafc"><tr>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">IP Address</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">MAC Address</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Hostname</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Server</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Expires</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Status</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($leases as $l)
                <tr class="hover:bg-orange-50/20">
                    <td class="px-4 py-2.5 text-blue-700 font-mono text-xs font-bold">{{ $l['address'] ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">{{ $l['mac-address'] ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-gray-700 text-xs">{{ $l['host-name'] ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-gray-500 text-xs">{{ $l['server'] ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-gray-400 text-xs">{{ $l['expires-after'] ?? '-' }}</td>
                    <td class="px-4 py-2.5"><span class="px-2 py-0.5 rounded-full text-xs {{ ($l['status'] ?? '') === 'bound' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ ucfirst($l['status'] ?? 'waiting') }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-8 text-center text-gray-400">No DHCP leases</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="px-4 py-3 border-b border-gray-100"><h3 class="text-gray-800 font-bold text-sm">ARP Table ({{ count($arp) }})</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead style="background:#f8fafc"><tr>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">IP Address</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">MAC Address</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Interface</th>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Status</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($arp as $a)
                <tr class="hover:bg-orange-50/20">
                    <td class="px-4 py-2.5 text-blue-700 font-mono text-xs font-bold">{{ $a['address'] ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">{{ $a['mac-address'] ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-gray-600 text-xs">{{ $a['interface'] ?? '-' }}</td>
                    <td class="px-4 py-2.5"><span class="px-2 py-0.5 rounded-full text-xs {{ ($a['complete'] ?? '') === 'true' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ ($a['complete'] ?? '') === 'true' ? 'Complete' : 'Incomplete' }}</span></td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-8 text-center text-gray-400">No ARP entries</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
