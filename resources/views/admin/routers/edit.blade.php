@extends('layouts.admin')
@section('title','Edit Router')
@section('page-title','Edit Router — '.$router->name)
@section('page-subtitle','Update MikroTik connection details')
@section('content')
<div class="max-w-2xl">
<form action="{{ route('admin.routers.update', $router->id) }}" method="POST" class="space-y-4">
    @csrf @method('PUT')

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
        <h3 class="text-gray-800 font-bold text-sm"><i class="fas fa-router text-orange-500 mr-2"></i>Router Details</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Router Name *</label>
                <input type="text" name="name" value="{{ old('name', $router->name) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none @error('name') border-red-400 @enderror" required>
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">IP Address *</label>
                <input type="text" name="ip_address" value="{{ old('ip_address', $router->ip_address) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">API Port</label>
                <input type="number" name="api_port" value="{{ old('api_port', $router->api_port ?? 8728) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">API Username *</label>
                <input type="text" name="username" value="{{ old('username', $router->username) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none @error('username') border-red-400 @enderror" required>
                @error('username')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">API Password <span class="font-normal text-gray-400">(leave blank to keep current)</span></label>
                <input type="password" name="password" placeholder="Enter new password or leave blank" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Model</label>
                <input type="text" name="model" value="{{ old('model', $router->model ?? '') }}" placeholder="e.g. RB750Gr3" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
        <h3 class="text-gray-800 font-bold text-sm"><i class="fas fa-server text-blue-500 mr-2"></i>Assignment</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">NAS Server</label>
                <select name="nas_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- None --</option>
                    @foreach($nas as $n)
                    <option value="{{ $n->id }}" {{ old('nas_id',$router->nas_id)==$n->id?'selected':'' }}>{{ $n->shortname ?? $n->nasname ?? $n->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- OpenVPN -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-gray-800 font-bold text-sm"><i class="fas fa-shield-alt text-green-500 mr-2"></i>OpenVPN Tunnel</h3>
            <label class="flex items-center cursor-pointer">
                <div class="relative">
                    <input type="checkbox" name="use_ovpn" id="use_ovpn" class="sr-only" {{ old('use_ovpn',$router->use_ovpn)?'checked':'' }} onchange="toggleOvpn(this.checked)">
                    <div class="w-10 h-5 rounded-full shadow-inner" id="ovpn-track" style="background:{{ ($router->use_ovpn) ? '#f97316' : '#d1d5db' }}"></div>
                    <div class="dot absolute w-4 h-4 bg-white rounded-full shadow top-0.5 transition-transform" id="ovpn-dot" style="left:2px;transform:{{ $router->use_ovpn ? 'translateX(20px)' : 'translateX(0)' }}"></div>
                </div>
                <span class="ml-2 text-xs text-gray-600 font-semibold">Enable OVPN</span>
            </label>
        </div>
        <div id="ovpn-fields" class="{{ old('use_ovpn',$router->use_ovpn) ? '' : 'hidden' }} grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">VPS / OVPN Gateway IP</label>
                <input type="text" name="ovpn_gateway" value="{{ old('ovpn_gateway',$router->ovpn_gateway) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">OVPN Username</label>
                <input type="text" name="ovpn_username" value="{{ old('ovpn_username',$router->ovpn_username) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">OVPN Password</label>
                <input type="password" name="ovpn_password" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="Leave blank to keep current">
            </div>
        </div>
    </div>

    <div class="flex justify-between">
        <a href="{{ route('admin.routers.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">← Back</a>
        <div class="flex gap-2">
            <form action="{{ route('admin.routers.sync', $router->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2.5 border border-green-200 text-green-700 rounded-xl text-sm font-semibold hover:bg-green-50">
                    <i class="fas fa-plug mr-1"></i>Test Connection
                </button>
            </form>
            <button type="submit" class="px-6 py-2.5 text-white rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
                <i class="fas fa-save mr-1"></i>Save Changes
            </button>
        </div>
    </div>
</form>
</div>
<script>
function toggleOvpn(on) {
    document.getElementById('ovpn-fields').classList.toggle('hidden', !on);
    document.getElementById('ovpn-track').style.background = on ? '#f97316' : '#d1d5db';
    document.getElementById('ovpn-dot').style.transform    = on ? 'translateX(20px)' : 'translateX(0)';
}
</script>
@endsection
