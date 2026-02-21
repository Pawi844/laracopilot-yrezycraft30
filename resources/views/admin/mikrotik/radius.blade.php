@extends('layouts.admin')
@section('title','RADIUS — ' . $router->name)
@section('page-title','RADIUS Configuration')
@section('page-subtitle', $router->name . ' · Manage RADIUS authentication')
@section('content')
@include('admin.mikrotik._nav')

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <!-- Current RADIUS Servers on Router -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-4 py-3 border-b border-gray-100"><h3 class="text-gray-800 font-bold text-sm">RADIUS Servers on MikroTik ({{ count($radiusServers) }})</h3></div>
        @if(empty($radiusServers))
        <div class="px-4 py-8 text-center">
            <i class="fas fa-satellite text-gray-200 text-4xl mb-2 block"></i>
            <p class="text-gray-400 text-sm">No RADIUS servers configured on this router.</p>
            <p class="text-gray-400 text-xs mt-1">Use the form to push this system as a RADIUS server.</p>
        </div>
        @else
        <div class="p-3 space-y-2">
            @foreach($radiusServers as $rs)
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="grid grid-cols-2 gap-2">
                    <div><p class="text-gray-400 text-xs">Service</p><p class="text-gray-800 font-semibold text-xs">{{ $rs['service'] ?? '-' }}</p></div>
                    <div><p class="text-gray-400 text-xs">Server IP</p><p class="text-blue-700 font-mono font-bold text-xs">{{ $rs['address'] ?? '-' }}</p></div>
                    <div><p class="text-gray-400 text-xs">Auth Port</p><p class="text-gray-700 font-mono text-xs">{{ $rs['authentication-port'] ?? '1812' }}</p></div>
                    <div><p class="text-gray-400 text-xs">Acct Port</p><p class="text-gray-700 font-mono text-xs">{{ $rs['accounting-port'] ?? '1813' }}</p></div>
                    <div><p class="text-gray-400 text-xs">Timeout</p><p class="text-gray-700 text-xs">{{ $rs['timeout'] ?? '3000ms' }}</p></div>
                    <div><p class="text-gray-400 text-xs">Status</p><span class="px-2 py-0.5 rounded-full text-xs {{ (isset($rs['disabled']) && $rs['disabled'] === 'true') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">{{ (isset($rs['disabled']) && $rs['disabled'] === 'true') ? 'Disabled' : 'Active' }}</span></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Push RADIUS Config -->
    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-orange-100 shadow-sm p-5">
            <h3 class="text-gray-800 font-bold text-sm mb-1"><i class="fas fa-satellite text-orange-500 mr-2"></i>Push RADIUS Server to MikroTik</h3>
            <p class="text-gray-400 text-xs mb-4">This will add this ISP system as a RADIUS authentication server on the MikroTik router.</p>
            <form action="{{ route('admin.mikrotik.radius.push', $router->id) }}" method="POST" class="space-y-3">
                @csrf
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">NAS / RADIUS Server IP *</label><input name="nas_ip" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="192.168.1.10 (this server's IP)" required></div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">RADIUS Secret *</label><input name="secret" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="shared_secret" required></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-semibold text-gray-600 mb-1">Auth Port</label><input name="auth_port" value="1812" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
                    <div><label class="block text-xs font-semibold text-gray-600 mb-1">Acct Port</label><input name="acct_port" value="1813" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
                </div>
                <button type="submit" class="w-full text-white py-2.5 rounded-xl text-sm font-bold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
                    <i class="fas fa-upload mr-2"></i>Push RADIUS Config to Router
                </button>
            </form>
        </div>

        <!-- Sync Users -->
        <div class="bg-white rounded-xl border border-blue-100 shadow-sm p-5">
            <h3 class="text-gray-800 font-bold text-sm mb-1"><i class="fas fa-sync text-blue-500 mr-2"></i>Sync Clients to MikroTik</h3>
            <p class="text-gray-400 text-xs mb-4">Push all active clients assigned to this router as PPPoE secrets or Hotspot users directly on MikroTik.</p>
            <form action="{{ route('admin.mikrotik.sync', $router->id) }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-xl text-sm font-bold hover:bg-blue-700">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>Sync All Clients to MikroTik
                </button>
            </form>
        </div>

        <!-- RADIUS Setup Guide -->
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
            <h4 class="text-gray-700 font-bold text-xs mb-2"><i class="fas fa-info-circle text-blue-500 mr-1"></i>MikroTik RADIUS Setup Checklist</h4>
            <ul class="text-gray-500 text-xs space-y-1">
                <li class="flex items-start space-x-1.5"><i class="fas fa-check text-green-500 mt-0.5"></i><span>Enable API: <code class="bg-white px-1 rounded">/ip service set api disabled=no</code></span></li>
                <li class="flex items-start space-x-1.5"><i class="fas fa-check text-green-500 mt-0.5"></i><span>Enable PPPoE RADIUS: <code class="bg-white px-1 rounded">/ppp aaa set use-radius=yes</code></span></li>
                <li class="flex items-start space-x-1.5"><i class="fas fa-check text-green-500 mt-0.5"></i><span>Enable Hotspot RADIUS in hotspot server profile</span></li>
                <li class="flex items-start space-x-1.5"><i class="fas fa-check text-green-500 mt-0.5"></i><span>Allow port 1812/1813 UDP through firewall</span></li>
                <li class="flex items-start space-x-1.5"><i class="fas fa-check text-green-500 mt-0.5"></i><span>API port 8728 must be accessible from this server</span></li>
            </ul>
        </div>
    </div>
</div>
@endsection
