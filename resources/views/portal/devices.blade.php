@extends('layouts.portal')
@section('title','My Devices')
@section('content')
<div class="space-y-5">
    @forelse($devices as $device)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 flex justify-between items-center" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
            <div class="flex items-center space-x-3">
                <i class="fas fa-router text-orange-400 text-xl"></i>
                <div>
                    <p class="text-white font-bold">{{ $device->model ?? 'ONU Device' }}</p>
                    <p class="text-blue-200 text-xs font-mono">SN: {{ $device->serial_number }}</p>
                </div>
            </div>
            <span class="text-xs px-2.5 py-1 rounded-full font-bold {{ $device->onu_status === 'online' ? 'bg-green-400/20 text-green-300' : 'bg-red-400/20 text-red-300' }}">
                <span class="w-1.5 h-1.5 rounded-full inline-block mr-1 {{ $device->onu_status === 'online' ? 'bg-green-400 animate-pulse' : 'bg-red-400' }}"></span>
                {{ ucfirst($device->onu_status ?? 'Unknown') }}
            </span>
        </div>
        <div class="p-5 grid grid-cols-2 md:grid-cols-3 gap-4">
            <div><p class="text-gray-400 text-xs">WiFi Name (SSID)</p><p class="text-gray-800 font-bold">{{ $device->wlan_ssid ?? 'Not set' }}</p></div>
            <div><p class="text-gray-400 text-xs">WAN IP</p><p class="text-blue-700 font-mono font-bold">{{ $device->wan_external_ip ?? '-' }}</p></div>
            <div><p class="text-gray-400 text-xs">RX Signal</p>
                @if($device->opt_rx_power !== null)
                @php $rx=(float)$device->opt_rx_power;$c=$rx>=-20?'text-green-600':($rx>=-25?'text-yellow-600':'text-red-600'); @endphp
                <p class="{{ $c }} font-bold">{{ number_format($rx,2) }} dBm ({{ $device->rx_signal_quality }})</p>
                @else<p class="text-gray-400">N/A</p>@endif
            </div>
            <div><p class="text-gray-400 text-xs">TX Power</p><p class="text-gray-700 font-semibold">{{ $device->opt_tx_power ? number_format($device->opt_tx_power,2).' dBm' : 'N/A' }}</p></div>
            <div><p class="text-gray-400 text-xs">MAC Address</p><p class="text-gray-700 font-mono text-xs">{{ $device->mac_address ?? '-' }}</p></div>
            <div><p class="text-gray-400 text-xs">Last Seen</p><p class="text-gray-700 text-sm">{{ $device->last_inform?->diffForHumans() ?? 'Never' }}</p></div>
        </div>
        <!-- WiFi Password Change -->
        <div class="border-t border-gray-100 p-5">
            <h3 class="text-gray-700 font-bold text-sm mb-3"><i class="fas fa-wifi text-orange-500 mr-1"></i>Change WiFi Settings</h3>
            <form action="{{ route('portal.change_wifi') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @csrf
                <input type="hidden" name="device_id" value="{{ $device->id }}">
                <div><label class="text-xs text-gray-500 mb-1 block">WiFi Name (SSID)</label>
                    <input type="text" name="ssid" value="{{ $device->wlan_ssid }}" maxlength="32" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                </div>
                <div><label class="text-xs text-gray-500 mb-1 block">New WiFi Password</label>
                    <input type="text" name="wifi_password" placeholder="Min 8 characters" minlength="8" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full text-white py-2 rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Apply Changes</button>
                </div>
            </form>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <i class="fas fa-router text-5xl text-gray-200 mb-3 block"></i>
        <h3 class="text-gray-600 font-bold">No Devices Found</h3>
        <p class="text-gray-400 text-sm mt-1">No ONU/CPE devices linked to your account. Contact support if you believe this is an error.</p>
    </div>
    @endforelse
</div>
@endsection
