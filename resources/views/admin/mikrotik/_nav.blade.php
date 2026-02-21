@php
$mkTabs = [
    ['route' => 'admin.mikrotik.dashboard', 'icon' => 'fa-tachometer-alt', 'label' => 'Overview'],
    ['route' => 'admin.mikrotik.interfaces', 'icon' => 'fa-ethernet', 'label' => 'Interfaces'],
    ['route' => 'admin.mikrotik.pppoe', 'icon' => 'fa-plug', 'label' => 'PPPoE'],
    ['route' => 'admin.mikrotik.hotspot', 'icon' => 'fa-wifi', 'label' => 'Hotspot'],
    ['route' => 'admin.mikrotik.ip_pools', 'icon' => 'fa-list-ol', 'label' => 'IP Pools'],
    ['route' => 'admin.mikrotik.queues', 'icon' => 'fa-layer-group', 'label' => 'Queues'],
    ['route' => 'admin.mikrotik.firewall', 'icon' => 'fa-shield-alt', 'label' => 'Firewall'],
    ['route' => 'admin.mikrotik.dhcp', 'icon' => 'fa-network-wired', 'label' => 'DHCP/ARP'],
    ['route' => 'admin.mikrotik.routes', 'icon' => 'fa-route', 'label' => 'Routes'],
    ['route' => 'admin.mikrotik.wireless', 'icon' => 'fa-broadcast-tower', 'label' => 'Wireless'],
    ['route' => 'admin.mikrotik.radius', 'icon' => 'fa-satellite', 'label' => 'RADIUS'],
];
@endphp
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:linear-gradient(135deg,#1e3a5f,#0f2744)">
                <i class="fas fa-router text-white text-xs"></i>
            </div>
            <div>
                <p class="text-gray-800 font-bold text-sm">{{ $router->name }}</p>
                <p class="text-blue-600 font-mono text-xs">{{ $router->ip_address }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            @if($connected ?? true)
            <span class="flex items-center space-x-1 bg-green-100 text-green-700 text-xs px-2.5 py-1 rounded-full font-semibold">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span><span>Connected</span>
            </span>
            @else
            <span class="flex items-center space-x-1 bg-red-100 text-red-700 text-xs px-2.5 py-1 rounded-full font-semibold">
                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span><span>Unreachable</span>
            </span>
            @endif
            <a href="{{ route('admin.mikrotik.select') }}" class="text-xs text-gray-400 hover:text-gray-600"><i class="fas fa-exchange-alt mr-1"></i>Switch</a>
        </div>
    </div>
    <div class="flex flex-wrap gap-1.5">
        @foreach($mkTabs as $tab)
        <a href="{{ route($tab['route'], $router->id) }}" class="mk-tab {{ request()->routeIs($tab['route']) ? 'active' : '' }}">
            <i class="fas {{ $tab['icon'] }}"></i>{{ $tab['label'] }}
        </a>
        @endforeach
    </div>
</div>
