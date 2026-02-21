@extends('layouts.admin')
@section('title','Edit Router')
@section('page-title','Edit Router')
@section('page-subtitle', $router->name)
@section('content')
<div class="max-w-2xl">
<form action="{{ route('admin.routers.update',$router->id) }}" method="POST" class="space-y-4">
    @csrf @method('PUT')
    <!-- Basic -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 sm:p-6">
        <h3 class="text-gray-800 font-bold text-sm mb-4"><i class="fas fa-router text-orange-500 mr-2"></i>Router Details</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Router Name *</label>
                <input type="text" name="name" value="{{ old('name',$router->name) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">IP Address / Hostname *</label>
                <input type="text" name="ip_address" value="{{ old('ip_address',$router->ip_address) }}" placeholder="41.80.x.x or router.domain.com" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">API Port</label>
                <input type="number" name="api_port" value="{{ old('api_port',$router->api_port??8728) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">API Username</label>
                <input type="text" name="api_username" value="{{ old('api_username',$router->api_username) }}" placeholder="admin" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">API Password</label>
                <input type="password" name="api_password" placeholder="{{ $router->api_password ? '●●●●●●●●' : 'Enter password' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
        </div>
    </div>

    <!-- OpenVPN -->
    <div class="bg-white rounded-xl border border-orange-200 shadow-sm p-5 sm:p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h3 class="text-gray-800 font-bold text-sm"><i class="fas fa-shield-alt text-orange-500 mr-2"></i>OpenVPN Tunnel</h3>
                <p class="text-gray-400 text-xs mt-0.5">Use if this router does NOT have a public IP address</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer mt-1">
                <input type="hidden" name="use_ovpn" value="0">
                <input type="checkbox" name="use_ovpn" value="1" id="use_ovpn" {{ old('use_ovpn',$router->use_ovpn)?'checked':'' }} class="sr-only peer" onchange="toggleOvpn(this)">
                <div class="w-10 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-orange-400 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-orange-500"></div>
            </label>
        </div>
        <div id="ovpn-fields" class="space-y-3 {{ old('use_ovpn',$router->use_ovpn)?'':'hidden' }}">
            <div class="bg-orange-50 border border-orange-100 rounded-lg p-3 text-xs text-orange-700 mb-3">
                <i class="fas fa-info-circle mr-1"></i>Set the <strong>Tunnel IP</strong> to the private IP this router's OVPN client receives (e.g. 10.8.0.2). The system will use this IP for API calls instead of the public IP.
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">OVPN Tunnel IP (e.g. 10.8.0.2)</label>
                    <input type="text" name="ovpn_gateway" value="{{ old('ovpn_gateway',$router->ovpn_gateway) }}" placeholder="10.8.0.2" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
                </div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">VPN Username</label>
                    <input type="text" name="ovpn_username" value="{{ old('ovpn_username',$router->ovpn_username) }}" placeholder="router-01" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                </div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">VPN Password</label>
                    <input type="password" name="ovpn_password" placeholder="{{ $router->ovpn_password ? '●●●●●●●●' : 'VPN password' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                </div>
                <div class="flex items-end">
                    <a href="{{ route('admin.routers.ovpn_config',$router->id) }}" class="w-full text-center border border-orange-300 text-orange-600 px-3 py-2.5 rounded-lg text-sm font-semibold hover:bg-orange-50 transition-colors">
                        <i class="fas fa-download mr-1"></i>Download .ovpn Config
                    </a>
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs">
                <p class="text-blue-700 font-semibold mb-1">MikroTik OVPN Client Setup:</p>
                <p class="text-blue-600 font-mono">/interface ovpn-client add name=ovpn-isp connect-to={{ \App\Models\SystemSetting::get('ovpn','server_ip','VPN_SERVER') }} port={{ \App\Models\SystemSetting::get('ovpn','server_port','1194') }} user={{ $router->ovpn_username ?: 'router-01' }} password=***</p>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-between gap-2">
        <a href="{{ route('admin.routers.index') }}" class="text-center px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600">← Cancel</a>
        <button type="submit" class="px-6 py-2.5 text-white rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Save Router</button>
    </div>
</form>
</div>
<script>
function toggleOvpn(cb) { document.getElementById('ovpn-fields').classList.toggle('hidden',!cb.checked); }
</script>
@endsection
