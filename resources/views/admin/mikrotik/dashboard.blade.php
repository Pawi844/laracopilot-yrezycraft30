@extends('layouts.admin')
@section('title','MikroTik Dashboard')
@section('page-title','MikroTik — '.$identity)
@section('page-subtitle','Live router data via RouterOS API')
@section('content')

<!-- Router selector + nav -->
<div class="flex flex-wrap gap-2 mb-4">
    <select onchange="window.location='/admin/mikrotik/'+this.value+'/dashboard'" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
        @foreach($routers as $r)
        <option value="{{ $r->id }}" {{ $r->id==$router->id?'selected':'' }}>{{ $r->name }} ({{ $r->ip_address }})</option>
        @endforeach
    </select>
    @foreach([
        ['pppoe','PPPoE','plug'],['hotspot','Hotspot','wifi'],['queues','Queues','sliders-h'],
        ['firewall','Firewall','shield-alt'],['dhcp','DHCP','network-wired'],['wireless','Wireless','broadcast-tower'],['radius','RADIUS','server'],
    ] as [$slug,$label,$icon])
    <a href="{{ route('admin.mikrotik.'.$slug, $router->id) }}" class="border border-gray-200 text-gray-600 px-3 py-2 rounded-lg text-xs font-semibold hover:bg-gray-50">
        <i class="fas fa-{{ $icon }} mr-1"></i>{{ $label }}
    </a>
    @endforeach
</div>

@if($error)
<div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
    <p class="text-red-700 font-bold text-sm"><i class="fas fa-times-circle mr-2"></i>Cannot reach MikroTik</p>
    <p class="text-red-600 text-xs mt-1">{{ $error }}</p>
    <div class="mt-3 text-xs text-red-500 space-y-1">
        <p>• Run on MikroTik: <code class="bg-red-100 rounded px-1">/ip service enable api</code></p>
        <p>• Create API user: <code class="bg-red-100 rounded px-1">/user add name={{ $router->username ?? 'api' }} password=X group=full</code></p>
        @if($router->use_ovpn)<p>• Verify OpenVPN tunnel to <strong>{{ $router->ovpn_gateway }}</strong> is up</p>@endif
    </div>
    <div class="mt-3 flex gap-2">
        <a href="{{ route('admin.routers.edit', $router->id) }}" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-lg font-semibold"><i class="fas fa-edit mr-1"></i>Edit Router Credentials</a>
        <form action="{{ route('admin.routers.sync', $router->id) }}" method="POST" class="inline">@csrf
            <button class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-lg font-semibold"><i class="fas fa-plug mr-1"></i>Test Connection</button>
        </form>
    </div>
</div>
@endif

<!-- DB stats (always shown) -->
<div class="grid grid-cols-3 gap-3 mb-4">
    @foreach([
        ['Total Clients',$dbStats['total_clients'],'fa-users','blue'],
        ['Active',$dbStats['active'],'fa-check-circle','green'],
        ['Suspended',$dbStats['suspended'],'fa-pause-circle','red'],
    ] as [$l,$v,$i,$c])
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-{{ $c }}-100 flex items-center justify-center flex-shrink-0">
            <i class="fas {{ $i }} text-{{ $c }}-600"></i>
        </div>
        <div><p class="text-gray-400 text-xs">{{ $l }}</p><p class="text-2xl font-black text-gray-800">{{ $v }}</p></div>
    </div>
    @endforeach
</div>

@if(!empty($sysRes))
<!-- System Resource -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    @php
        $uptime   = $sysRes['uptime'] ?? '—';
        $cpu      = $sysRes['cpu-load'] ?? '0';
        $memTotal = (int)($sysRes['total-memory'] ?? 0);
        $memFree  = (int)($sysRes['free-memory'] ?? 0);
        $memUsed  = $memTotal > 0 ? round((($memTotal-$memFree)/$memTotal)*100) : 0;
        $hddTotal = (int)($sysRes['total-hdd-space'] ?? 0);
        $hddFree  = (int)($sysRes['free-hdd-space'] ?? 0);
        $hddUsed  = $hddTotal > 0 ? round((($hddTotal-$hddFree)/$hddTotal)*100) : 0;
        $version  = $sysRes['version'] ?? '—';
        $board    = $sysRes['board-name'] ?? ($router->model ?? '—');
    @endphp
    @foreach([
        ['Uptime',$uptime,'fa-clock','purple'],
        ['CPU Load',$cpu.'%','fa-microchip','orange'],
        ['Memory Used',$memUsed.'%','fa-memory','blue'],
        ['Disk Used',$hddUsed.'%','fa-hdd','green'],
    ] as [$l,$v,$i,$c])
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-center space-x-2 mb-2">
            <i class="fas {{ $i }} text-{{ $c }}-500 text-sm"></i>
            <p class="text-gray-400 text-xs">{{ $l }}</p>
        </div>
        <p class="text-xl font-black text-gray-800">{{ $v }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <!-- Interfaces -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 text-sm"><i class="fas fa-ethernet text-blue-500 mr-2"></i>Interfaces ({{ count($ifaces) }})</h3>
        </div>
        <div class="overflow-auto max-h-64">
        <table class="w-full text-xs">
            <thead style="background:#f8fafc"><tr>
                <th class="px-3 py-2 text-left text-gray-500 font-semibold">Name</th>
                <th class="px-3 py-2 text-left text-gray-500 font-semibold">Type</th>
                <th class="px-3 py-2 text-left text-gray-500 font-semibold">Status</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
            @forelse($ifaces as $iface)
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-2 font-mono font-bold text-gray-700">{{ $iface['name'] ?? '—' }}</td>
                <td class="px-3 py-2 text-gray-500">{{ $iface['type'] ?? '—' }}</td>
                <td class="px-3 py-2">
                    <span class="@if(($iface['running']??'')=='true') bg-green-100 text-green-700 @else bg-red-100 text-red-600 @endif px-2 py-0.5 rounded-full font-semibold">
                        {{ ($iface['running']??'') === 'true' ? 'Up' : 'Down' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-3 py-6 text-center text-gray-400">No interfaces</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
    <!-- IP Addresses -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 text-sm"><i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>IP Addresses ({{ count($ipAddrs) }})</h3>
        </div>
        <div class="overflow-auto max-h-64">
        <table class="w-full text-xs">
            <thead style="background:#f8fafc"><tr>
                <th class="px-3 py-2 text-left text-gray-500 font-semibold">Address</th>
                <th class="px-3 py-2 text-left text-gray-500 font-semibold">Interface</th>
                <th class="px-3 py-2 text-left text-gray-500 font-semibold">Network</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
            @forelse($ipAddrs as $ip)
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-2 font-mono font-bold text-orange-600">{{ $ip['address'] ?? '—' }}</td>
                <td class="px-3 py-2 font-mono text-gray-600">{{ $ip['interface'] ?? '—' }}</td>
                <td class="px-3 py-2 font-mono text-gray-400">{{ $ip['network'] ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-3 py-6 text-center text-gray-400">No IP addresses</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
    <p class="text-gray-500 text-sm">
        <i class="fas fa-info-circle text-blue-400 mr-1"></i>
        RouterOS <strong>{{ $version }}</strong> on <strong>{{ $board }}</strong> · Identity: <strong>{{ $identity }}</strong>
    </p>
</div>
@endif

@endsection
