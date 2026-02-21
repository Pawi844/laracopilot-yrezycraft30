@extends('layouts.admin')
@section('title', 'ONU: ' . $device->serial_number)
@section('page-title', 'ONU Device Details')
@section('page-subtitle', $device->serial_number . ' · ' . ($device->manufacturer ?? 'Unknown Manufacturer'))

@section('content')
<div class="space-y-5">

    <!-- Header Bar -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#1e3a5f,#0f2744)">
                <i class="fas fa-broadcast-tower text-white text-xl"></i>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h2 class="text-gray-800 font-black text-lg">{{ $device->model ?? 'ONU Device' }}</h2>
                    @if($device->onu_status === 'online')
                        <span class="flex items-center space-x-1 bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-bold">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span><span>Online</span>
                        </span>
                    @else
                        <span class="flex items-center space-x-1 bg-red-100 text-red-700 px-2.5 py-1 rounded-full text-xs font-bold">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span><span>Offline</span>
                        </span>
                    @endif
                </div>
                <p class="text-gray-400 text-sm">{{ $device->manufacturer }} · SN: <span class="font-mono text-blue-700 font-semibold">{{ $device->serial_number }}</span></p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <form action="{{ route('admin.tr069.refresh', $device->id) }}" method="POST">
                @csrf
                <button class="flex items-center space-x-1.5 bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-blue-800">
                    <i class="fas fa-sync-alt"></i><span>Refresh</span>
                </button>
            </form>
            <form action="{{ route('admin.tr069.reboot', $device->id) }}" method="POST">
                @csrf
                <button onclick="return confirm('Send reboot command?')" class="flex items-center space-x-1.5 bg-red-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-red-700">
                    <i class="fas fa-power-off"></i><span>Reboot ONU</span>
                </button>
            </form>
            <a href="{{ route('admin.tr069.edit', $device->id) }}" class="flex items-center space-x-1.5 text-white px-4 py-2 rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
                <i class="fas fa-edit"></i><span>Edit</span>
            </a>
            <a href="{{ route('admin.tr069.index') }}" class="flex items-center space-x-1.5 border border-gray-200 text-gray-600 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i><span>Back</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- LEFT COLUMN -->
        <div class="space-y-5">

            <!-- General Information -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 flex items-center space-x-2" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
                    <i class="fas fa-info-circle text-orange-400"></i>
                    <h3 class="text-white font-bold text-sm">General Information</h3>
                    @if($device->onu_status === 'online')
                        <span class="ml-auto flex items-center space-x-1 text-green-300 text-xs"><span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span><span>Online</span></span>
                    @else
                        <span class="ml-auto flex items-center space-x-1 text-red-300 text-xs"><span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span><span>Offline</span></span>
                    @endif
                </div>
                <div class="divide-y divide-gray-50">
                    @php
                    $generalRows = [
                        ['Hardware Version', $device->hardware_version ?? 'N/A', 'fa-microchip', 'text-purple-600'],
                        ['Software Version', $device->software_version ?? 'N/A', 'fa-code', 'text-blue-600'],
                        ['Last Update', $device->last_update ? $device->last_update->format('d/m/Y H:i:s') : 'N/A', 'fa-clock', 'text-gray-500'],
                        ['Create Date', $device->create_date ? $device->create_date->format('d/m/Y H:i:s') : 'N/A', 'fa-calendar', 'text-gray-500'],
                        ['MAC', $device->mac_address ?? 'N/A', 'fa-network-wired', 'text-blue-700'],
                        ['Manufacturer', $device->manufacturer ?? 'N/A', 'fa-industry', 'text-gray-600'],
                        ['OUI', $device->oui ?? 'N/A', 'fa-hashtag', 'text-gray-500'],
                        ['Product Class', $device->product_class ?? 'N/A', 'fa-box', 'text-orange-600'],
                        ['Serial Number', $device->serial_number, 'fa-barcode', 'text-blue-700'],
                        ['Wlan SSID', $device->wlan_ssid ?? 'N/A', 'fa-wifi', 'text-green-600'],
                        ['Device ID', $device->device_id ?? 'N/A', 'fa-fingerprint', 'text-gray-500'],
                    ];
                    @endphp
                    @foreach($generalRows as $row)
                    <div class="px-4 py-2.5 flex items-start justify-between hover:bg-gray-50/80">
                        <div class="flex items-center space-x-2 flex-shrink-0">
                            <i class="fas {{ $row[2] }} {{ $row[3] }} text-xs w-3"></i>
                            <span class="text-gray-500 text-xs">{{ $row[0] }}</span>
                        </div>
                        <span class="text-gray-800 text-xs font-semibold font-mono ml-3 text-right break-all">{{ $row[1] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Assigned Client -->
            @if($device->client)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 flex items-center space-x-2" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
                    <i class="fas fa-user text-orange-400"></i>
                    <h3 class="text-white font-bold text-sm">Assigned Client</h3>
                </div>
                <div class="p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-black" style="background:linear-gradient(135deg,#f97316,#ea580c)">
                            {{ strtoupper(substr($device->client->first_name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-gray-800 font-bold">{{ $device->client->full_name }}</p>
                            <p class="text-blue-600 font-mono text-xs">{{ $device->client->username }}</p>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1.5">
                        <div class="flex justify-between"><span class="text-gray-400 text-xs">Phone</span><span class="text-gray-700 text-xs">{{ $device->client->phone ?? '-' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-400 text-xs">Plan</span><span class="text-gray-700 text-xs">{{ $device->client->plan->name ?? '-' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-400 text-xs">Status</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $device->client->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($device->client->status) }}</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.clients.show', $device->client->id) }}" class="mt-3 block text-center text-xs text-orange-500 hover:underline font-semibold">View Full Profile →</a>
                </div>
            </div>
            @endif

        </div>

        <!-- RIGHT COLUMN -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Optical Information -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 flex items-center space-x-2" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
                    <i class="fas fa-wave-square text-orange-400"></i>
                    <h3 class="text-white font-bold text-sm">Optical Information</h3>
                    <span class="ml-auto text-blue-200 text-xs">Fiber Signal Levels</span>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">

                        <!-- Temperature -->
                        <div class="bg-orange-50 border border-orange-100 rounded-xl p-4 text-center">
                            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-thermometer-half text-orange-500"></i>
                            </div>
                            <p class="text-2xl font-black text-orange-600">{{ number_format((float)($device->opt_temperature ?? 0), 2) }}</p>
                            <p class="text-xs text-orange-500 font-semibold">°C</p>
                            <p class="text-gray-400 text-xs mt-1">Temperature</p>
                            @php
                            $temp = (float)($device->opt_temperature ?? 0);
                            $tempColor = $temp > 60 ? 'text-red-600' : ($temp > 50 ? 'text-yellow-600' : 'text-green-600');
                            $tempLabel = $temp > 60 ? 'Critical' : ($temp > 50 ? 'Warm' : 'Normal');
                            @endphp
                            <p class="text-xs font-bold {{ $tempColor }} mt-1">{{ $tempLabel }}</p>
                        </div>

                        <!-- Voltage -->
                        <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4 text-center">
                            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-bolt text-yellow-500"></i>
                            </div>
                            <p class="text-2xl font-black text-yellow-600">{{ number_format((float)($device->opt_voltage ?? 0), 2) }}</p>
                            <p class="text-xs text-yellow-500 font-semibold">V</p>
                            <p class="text-gray-400 text-xs mt-1">Voltage</p>
                        </div>

                        <!-- TX Power -->
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-arrow-up text-blue-500"></i>
                            </div>
                            <p class="text-2xl font-black text-blue-600">{{ number_format((float)($device->opt_tx_power ?? 0), 2) }}</p>
                            <p class="text-xs text-blue-500 font-semibold">dBm</p>
                            <p class="text-gray-400 text-xs mt-1">TX Power</p>
                            @php
                            $tx = (float)($device->opt_tx_power ?? 0);
                            $txColor = $tx >= 0.5 ? 'text-green-600' : ($tx >= -3 ? 'text-yellow-600' : 'text-red-600');
                            @endphp
                            <p class="text-xs font-bold {{ $txColor }} mt-1">{{ $tx >= 0.5 ? 'Good' : ($tx >= -3 ? 'Marginal' : 'Low') }}</p>
                        </div>

                        <!-- RX Power -->
                        @php
                        $rx = (float)($device->opt_rx_power ?? 0);
                        $rxColors = [
                            'card' => $rx >= -20 ? 'bg-green-50 border-green-100' : ($rx >= -25 ? 'bg-yellow-50 border-yellow-100' : 'bg-red-50 border-red-100'),
                            'icon_bg' => $rx >= -20 ? 'bg-green-100' : ($rx >= -25 ? 'bg-yellow-100' : 'bg-red-100'),
                            'icon' => $rx >= -20 ? 'text-green-500' : ($rx >= -25 ? 'text-yellow-500' : 'text-red-500'),
                            'value' => $rx >= -20 ? 'text-green-600' : ($rx >= -25 ? 'text-yellow-600' : 'text-red-600'),
                            'unit' => $rx >= -20 ? 'text-green-500' : ($rx >= -25 ? 'text-yellow-500' : 'text-red-500'),
                        ];
                        @endphp
                        <div class="{{ $rxColors['card'] }} border rounded-xl p-4 text-center">
                            <div class="w-10 h-10 {{ $rxColors['icon_bg'] }} rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-arrow-down {{ $rxColors['icon'] }}"></i>
                            </div>
                            <p class="text-2xl font-black {{ $rxColors['value'] }}">{{ number_format($rx, 2) }}</p>
                            <p class="text-xs {{ $rxColors['unit'] }} font-semibold">dBm</p>
                            <p class="text-gray-400 text-xs mt-1">RX Power</p>
                            <p class="text-xs font-bold {{ $rxColors['value'] }} mt-1">{{ $device->rx_signal_quality }}</p>
                        </div>

                        <!-- Bias Current -->
                        <div class="bg-purple-50 border border-purple-100 rounded-xl p-4 text-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-tachometer-alt text-purple-500"></i>
                            </div>
                            <p class="text-2xl font-black text-purple-600">{{ number_format((float)($device->opt_bias_current ?? 0), 2) }}</p>
                            <p class="text-xs text-purple-500 font-semibold">mA</p>
                            <p class="text-gray-400 text-xs mt-1">Bias Current</p>
                        </div>
                    </div>

                    <!-- RX Signal Bar -->
                    <div class="mt-5 bg-gray-50 rounded-xl p-4">
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-gray-600 text-xs font-semibold">RX Signal Strength</p>
                            <span class="text-xs font-bold {{ $rx >= -20 ? 'text-green-600' : ($rx >= -25 ? 'text-yellow-600' : 'text-red-600') }}">{{ number_format($rx, 2) }} dBm · {{ $device->rx_signal_quality }}</span>
                        </div>
                        @php
                        // Scale -30 to 0 dBm → 0 to 100%
                        $pct = max(0, min(100, (($rx + 30) / 30) * 100));
                        $barColor = $rx >= -20 ? '#22c55e' : ($rx >= -25 ? '#eab308' : '#ef4444');
                        @endphp
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all" style="width:{{ $pct }}%;background:{{ $barColor }}"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span>-30 dBm (Critical)</span>
                            <span>-25 (Fair)</span>
                            <span>-20 (Good)</span>
                            <span>0 dBm (Excellent)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Network Info (LAN Clients) -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 flex items-center space-x-2" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
                    <i class="fas fa-laptop text-orange-400"></i>
                    <h3 class="text-white font-bold text-sm">Network Info</h3>
                    <span class="ml-auto bg-blue-900/50 text-blue-200 text-xs px-2 py-0.5 rounded-full">{{ count($device->lan_clients ?? []) }} device(s) connected</span>
                </div>
                @php $lanClients = $device->lan_clients ?? []; @endphp
                @if(count($lanClients))
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead style="background:#f8fafc">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Hostname / Device</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">IP Address</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">MAC Address</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Interface</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($lanClients as $lc)
                            <tr class="hover:bg-orange-50/30">
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-{{ isset($lc['interface']) && str_contains(strtolower($lc['interface'] ?? ''), 'wlan') ? 'mobile-alt' : 'laptop' }} text-gray-500 text-xs"></i>
                                        </div>
                                        <span class="text-gray-800 font-semibold text-sm">{{ $lc['hostname'] ?? $lc['name'] ?? 'Unknown Device' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-blue-700 font-mono text-sm font-bold">{{ $lc['ip'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $lc['mac'] ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @php $iface = strtolower($lc['interface'] ?? 'lan'); @endphp
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ str_contains($iface,'wlan') || str_contains($iface,'wifi') ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                        <i class="fas {{ str_contains($iface,'wlan') || str_contains($iface,'wifi') ? 'fa-wifi' : 'fa-ethernet' }} mr-1"></i>
                                        {{ strtoupper($lc['interface'] ?? 'LAN') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="px-4 py-8 text-center">
                    <i class="fas fa-laptop text-gray-200 text-4xl mb-2 block"></i>
                    <p class="text-gray-400 text-sm">No LAN clients reported.</p>
                    <p class="text-gray-400 text-xs mt-1">Client data is populated via TR-069 ACS inform messages.</p>
                </div>
                @endif
            </div>

            <!-- WAN PPP Info -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 flex items-center space-x-2" style="background:linear-gradient(90deg,#1e3a5f,#0f2744)">
                    <i class="fas fa-globe text-orange-400"></i>
                    <h3 class="text-white font-bold text-sm">WAN PPP Info</h3>
                    <span class="ml-auto text-blue-200 text-xs">Internet Connection</span>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-5">
                            <div class="flex items-center space-x-2 mb-3">
                                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-globe text-white text-sm"></i>
                                </div>
                                <p class="text-blue-800 font-bold text-sm">External IP Address</p>
                            </div>
                            <p class="text-blue-900 font-black text-2xl font-mono">{{ $device->wan_external_ip ?? 'Not Assigned' }}</p>
                            @if($device->wan_external_ip)
                            <p class="text-blue-600 text-xs mt-1">Public internet address</p>
                            @else
                            <p class="text-red-500 text-xs mt-1">No WAN IP — connection may be down</p>
                            @endif
                        </div>
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 rounded-xl p-5">
                            <div class="flex items-center space-x-2 mb-3">
                                <div class="w-8 h-8 bg-gray-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-network-wired text-white text-sm"></i>
                                </div>
                                <p class="text-gray-700 font-bold text-sm">WAN MAC Address</p>
                            </div>
                            <p class="text-gray-800 font-black text-xl font-mono">{{ $device->wan_mac_address ?? $device->mac_address ?? 'N/A' }}</p>
                            <p class="text-gray-500 text-xs mt-1">Hardware identifier</p>
                        </div>
                    </div>

                    @if($device->wan_connection_type)
                    <div class="mt-4 bg-orange-50 border border-orange-100 rounded-xl px-4 py-3 flex items-center space-x-3">
                        <i class="fas fa-plug text-orange-500"></i>
                        <div>
                            <p class="text-gray-500 text-xs">Connection Type</p>
                            <p class="text-gray-800 font-bold">{{ $device->wan_connection_type }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Device Timeline -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-gray-800 font-bold text-sm mb-4"><i class="fas fa-history text-orange-500 mr-2"></i>Device Timeline</h3>
                <div class="relative">
                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-100"></div>
                    <div class="space-y-4">
                        @foreach([
                            ['Create Date', $device->create_date ? $device->create_date->format('d M Y, H:i:s') : 'N/A', 'fa-plus-circle', 'text-green-500', 'First registered in system'],
                            ['Last TR-069 Inform', $device->last_inform ? $device->last_inform->format('d M Y, H:i:s') : 'N/A', 'fa-satellite-dish', 'text-blue-500', 'Last ACS contact'],
                            ['Last Update', $device->last_update ? $device->last_update->format('d M Y, H:i:s') : 'N/A', 'fa-sync', 'text-orange-500', 'Configuration last updated'],
                        ] as $event)
                        <div class="relative flex items-start space-x-4 pl-2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 bg-white border-2 border-gray-100 z-10">
                                <i class="fas {{ $event[2] }} {{ $event[3] }} text-xs"></i>
                            </div>
                            <div class="flex-1 pb-1">
                                <p class="text-gray-800 font-semibold text-xs">{{ $event[0] }}</p>
                                <p class="text-blue-700 font-mono text-xs">{{ $event[1] }}</p>
                                <p class="text-gray-400 text-xs">{{ $event[4] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
