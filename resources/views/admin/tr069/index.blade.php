@extends('layouts.admin')
@section('title','TR-069 Devices')
@section('page-title','TR-069 / ONU Device Management')
@section('page-subtitle','CPE devices managed via TR-069 ACS protocol')
@section('content')
<div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center space-x-3">
        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center"><i class="fas fa-check-circle text-green-600"></i></div>
        <div><p class="text-2xl font-black text-green-600">{{ $online }}</p><p class="text-gray-400 text-xs">Online</p></div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center space-x-3">
        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center"><i class="fas fa-times-circle text-red-500"></i></div>
        <div><p class="text-2xl font-black text-red-500">{{ $offline }}</p><p class="text-gray-400 text-xs">Offline</p></div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center space-x-3">
        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center"><i class="fas fa-router text-blue-600"></i></div>
        <div><p class="text-2xl font-black text-blue-700">{{ $devices->total() }}</p><p class="text-gray-400 text-xs">Total</p></div>
    </div>
</div>
<div class="flex justify-between items-center mb-4">
    <p class="text-gray-500 text-sm">All registered ONU/CPE devices</p>
    <a href="{{ route('admin.tr069.create') }}" class="text-white px-4 py-2 rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
        <i class="fas fa-plus mr-1"></i>Register Device
    </a>
</div>
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Serial / Model</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Manufacturer</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">SSID / IP</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">WAN IP</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Client</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">RX Signal</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Last Seen</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($devices as $d)
            <tr class="hover:bg-orange-50/30 transition-colors">
                <td class="px-4 py-3">
                    <a href="{{ route('admin.tr069.show', $d->id) }}" class="text-blue-700 font-mono text-xs font-bold hover:underline">{{ $d->serial_number }}</a>
                    <p class="text-gray-400 text-xs">{{ $d->product_class ?? $d->model ?? '-' }}</p>
                </td>
                <td class="px-4 py-3">
                    <p class="text-gray-700 font-medium text-xs">{{ Str::words($d->manufacturer ?? 'Unknown', 3, '') }}</p>
                    <p class="text-gray-400 text-xs">HW: {{ $d->hardware_version ?? '-' }}</p>
                </td>
                <td class="px-4 py-3">
                    @if($d->wlan_ssid)<p class="text-green-700 text-xs font-semibold"><i class="fas fa-wifi mr-1"></i>{{ $d->wlan_ssid }}</p>@endif
                    <p class="text-gray-400 font-mono text-xs">{{ $d->ip_address ?? '-' }}</p>
                </td>
                <td class="px-4 py-3 text-blue-600 font-mono text-xs font-semibold">{{ $d->wan_external_ip ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-600 text-xs">{{ $d->client->username ?? '-' }}</td>
                <td class="px-4 py-3">
                    @if($d->opt_rx_power !== null)
                    @php
                    $rx = (float)$d->opt_rx_power;
                    $rc = $rx >= -20 ? 'text-green-600' : ($rx >= -25 ? 'text-yellow-600' : 'text-red-600');
                    @endphp
                    <span class="{{ $rc }} font-mono text-xs font-bold">{{ number_format($rx,2) }} dBm</span>
                    <p class="{{ $rc }} text-xs">{{ $d->rx_signal_quality }}</p>
                    @else
                    <span class="text-gray-300 text-xs">N/A</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $d->onu_status === 'online'  ? 'bg-green-100 text-green-700' : '' }}
                        {{ $d->onu_status === 'offline' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $d->onu_status === 'unknown' ? 'bg-gray-100 text-gray-600' : '' }}">
                        <span class="w-1.5 h-1.5 rounded-full mr-1 {{ $d->onu_status === 'online' ? 'bg-green-500 animate-pulse' : 'bg-gray-400' }}"></span>
                        {{ ucfirst($d->onu_status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $d->last_inform ? $d->last_inform->diffForHumans() : 'Never' }}</td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end space-x-2">
                        <a href="{{ route('admin.tr069.show', $d->id) }}" class="text-blue-500 hover:text-blue-700 text-xs" title="View Details"><i class="fas fa-eye"></i></a>
                        <form action="{{ route('admin.tr069.reboot', $d->id) }}" method="POST" class="inline">
                            @csrf <button class="text-orange-500 hover:text-orange-700 text-xs" title="Reboot"><i class="fas fa-power-off"></i></button>
                        </form>
                        <a href="{{ route('admin.tr069.edit', $d->id) }}" class="text-blue-600 hover:text-blue-800 text-xs"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.tr069.destroy', $d->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Remove device?')" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="px-4 py-12 text-center text-gray-400">
                <i class="fas fa-router text-4xl mb-2 block text-gray-200"></i>No TR-069 devices registered.
            </td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $devices->links() }}</div>
</div>
@endsection
