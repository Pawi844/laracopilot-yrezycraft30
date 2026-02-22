@extends('layouts.admin')
@section('title','Add Router')
@section('page-title','Add MikroTik Router')
@section('page-subtitle','Connect a new MikroTik device via RouterOS API')
@section('content')
<div class="max-w-2xl">
<form action="{{ route('admin.routers.store') }}" method="POST" class="space-y-4">
    @csrf

    <!-- Basic Info -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
        <h3 class="text-gray-800 font-bold text-sm"><i class="fas fa-router text-orange-500 mr-2"></i>Router Details</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Router Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Mahigaini-Router-01" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none @error('name') border-red-400 @enderror" required>
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">IP Address *</label>
                <input type="text" name="ip_address" value="{{ old('ip_address') }}" placeholder="e.g. 41.90.239.131" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none @error('ip_address') border-red-400 @enderror" required>
                @error('ip_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">API Port</label>
                <input type="number" name="api_port" value="{{ old('api_port', 8728) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="8728">
                <p class="text-gray-400 text-xs mt-0.5">Default: 8728 (unencrypted) or 8729 (SSL)</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">API Username *</label>
                <input type="text" name="username" value="{{ old('username','admin') }}" placeholder="admin" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none @error('username') border-red-400 @enderror" required>
                @error('username')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">API Password *</label>
                <input type="password" name="password" placeholder="MikroTik user password" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none @error('password') border-red-400 @enderror" required>
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Model <span class="font-normal text-gray-400">(optional)</span></label>
                <input type="text" name="model" value="{{ old('model') }}" placeholder="e.g. RB750Gr3, hEX, CCR" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
        </div>
    </div>

    <!-- NAS / Reseller -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
        <h3 class="text-gray-800 font-bold text-sm"><i class="fas fa-server text-blue-500 mr-2"></i>Assignment</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">NAS Server</label>
                <select name="nas_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- None --</option>
                    @foreach($nas as $n)
                    <option value="{{ $n->id }}" {{ old('nas_id')==$n->id?'selected':'' }}>{{ $n->shortname ?? $n->nasname ?? $n->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Reseller</label>
                <select name="reseller_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- None --</option>
                    @foreach($resellers as $res)
                    <option value="{{ $res->id }}" {{ old('reseller_id')==$res->id?'selected':'' }}>{{ $res->company_name ?? $res->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- OpenVPN Tunnel -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-gray-800 font-bold text-sm"><i class="fas fa-shield-alt text-green-500 mr-2"></i>OpenVPN Tunnel</h3>
                <p class="text-gray-400 text-xs mt-0.5">If the router is behind NAT/CGNAT, connect via an OVPN tunnel to your VPS</p>
            </div>
            <label class="flex items-center cursor-pointer">
                <div class="relative">
                    <input type="checkbox" name="use_ovpn" id="use_ovpn" class="sr-only" {{ old('use_ovpn')?'checked':'' }} onchange="toggleOvpn(this.checked)">
                    <div class="w-10 h-5 bg-gray-300 rounded-full shadow-inner" id="ovpn-track"></div>
                    <div class="dot absolute w-4 h-4 bg-white rounded-full shadow top-0.5 left-0.5 transition-transform" id="ovpn-dot"></div>
                </div>
                <span class="ml-2 text-xs text-gray-600 font-semibold">Enable OVPN</span>
            </label>
        </div>
        <div id="ovpn-fields" class="{{ old('use_ovpn') ? '' : 'hidden' }} grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">VPS / OVPN Gateway IP</label>
                <input type="text" name="ovpn_gateway" value="{{ old('ovpn_gateway') }}" placeholder="e.g. 196.201.x.x" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">OVPN Username</label>
                <input type="text" name="ovpn_username" value="{{ old('ovpn_username') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">OVPN Password</label>
                <input type="password" name="ovpn_password" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
        </div>
    </div>

    <!-- MikroTik API Setup reminder -->
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-xs text-amber-800">
        <p class="font-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i>Enable RouterOS API on MikroTik first</p>
        <p>In Winbox or SSH run: <code class="bg-amber-100 rounded px-1">/ip service enable api</code> &nbsp;(port 8728)</p>
        <p class="mt-1">Create a dedicated API user: <code class="bg-amber-100 rounded px-1">/user add name=api password=yourpass group=full</code></p>
    </div>

    <div class="flex justify-between">
        <a href="{{ route('admin.routers.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">← Back</a>
        <button type="submit" class="px-6 py-2.5 text-white rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">
            <i class="fas fa-save mr-1"></i>Add Router
        </button>
    </div>
</form>
</div>

<style>
#ovpn-track { transition: background .2s; }
input[name=use_ovpn]:checked ~ * #ovpn-track,
#use_ovpn:checked + div { background: #f97316; }
</style>
<script>
function toggleOvpn(on) {
    document.getElementById('ovpn-fields').classList.toggle('hidden', !on);
    document.getElementById('ovpn-track').style.background = on ? '#f97316' : '#d1d5db';
    document.getElementById('ovpn-dot').style.transform    = on ? 'translateX(20px)' : 'translateX(0)';
}
// Init state
toggleOvpn(document.getElementById('use_ovpn').checked);
</script>
@endsection
