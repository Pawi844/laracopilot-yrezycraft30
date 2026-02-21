@extends('layouts.admin')
@section('title','Edit ONU Device')
@section('page-title','Edit ONU / TR-069 Device')
@section('page-subtitle','Update ACS URL, internet credentials, and device settings')
@section('content')
<div class="max-w-3xl">
<form action="{{ route('admin.tr069.update',$device->id) }}" method="POST" class="space-y-4">
    @csrf @method('PUT')
    <!-- Basic Info -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-gray-800 font-bold mb-4 text-sm"><i class="fas fa-router text-orange-500 mr-2"></i>Device Information</h3>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Serial Number *</label>
                <input type="text" name="serial_number" value="{{ old('serial_number',$device->serial_number) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">MAC Address</label>
                <input type="text" name="mac_address" value="{{ old('mac_address',$device->mac_address) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Model</label>
                <input type="text" name="model" value="{{ old('model',$device->model) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="e.g. HG8310M">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">WiFi SSID</label>
                <input type="text" name="wlan_ssid" value="{{ old('wlan_ssid',$device->wlan_ssid) }}" maxlength="32" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Client</label>
                <select name="client_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- Unassigned --</option>
                    @foreach($clients as $c)<option value="{{ $c->id }}" {{ old('client_id',$device->client_id)==$c->id?'selected':'' }}>{{ $c->username }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">FAT Node</label>
                <select name="fat_node_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- None --</option>
                    @foreach($fatNodes as $f)<option value="{{ $f->id }}" {{ old('fat_node_id',$device->fat_node_id)==$f->id?'selected':'' }}>{{ $f->name }} ({{ $f->code }})</option>@endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- ACS (TR-069) Settings -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-gray-800 font-bold text-sm"><i class="fas fa-broadcast-tower text-blue-500 mr-2"></i>ACS Connection Settings</h3>
            @if($globalAcsUrl)<p class="text-gray-400 text-xs">Global ACS: <span class="font-mono text-blue-600">{{ $globalAcsUrl }}</span></p>@endif
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">ACS URL <span class="font-normal text-gray-400">(leave blank to use global)</span></label>
                <input type="url" name="acs_url" value="{{ old('acs_url',$device->acs_url) }}" placeholder="http://acs.yourisp.com:7547" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">ACS Username</label>
                <input type="text" name="acs_username" value="{{ old('acs_username',$device->acs_username) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">ACS Password</label>
                <input type="password" name="acs_password" placeholder="{{ $device->acs_password ? '●●●●●●●●' : 'Enter password' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
        </div>
    </div>

    <!-- Connection Request (CPE calls ACS) -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-gray-800 font-bold text-sm mb-4"><i class="fas fa-plug text-purple-500 mr-2"></i>Connection Request <span class="font-normal text-gray-400 text-xs">(ONU → ACS, for remote management)</span></h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Connection Request URL</label>
                <input type="url" name="connection_request_url" value="{{ old('connection_request_url',$device->connection_request_url) }}" placeholder="http://192.168.1.1:7547" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">CR Username</label>
                <input type="text" name="connection_request_username" value="{{ old('connection_request_username',$device->connection_request_username) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">CR Password</label>
                <input type="password" name="connection_request_password" placeholder="{{ $device->connection_request_password ? '●●●●●●●●' : 'Enter' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
        </div>
    </div>

    <!-- Internet / PPPoE / WAN Credentials -->
    <div class="bg-white rounded-xl border border-orange-200 shadow-sm p-6">
        <h3 class="text-gray-800 font-bold text-sm mb-1"><i class="fas fa-globe text-orange-500 mr-2"></i>Internet (WAN) Credentials</h3>
        <p class="text-gray-400 text-xs mb-4">These are pushed to the ONU via TR-069 to configure the internet WAN connection (PPPoE or DHCP username/password).</p>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Internet Username <span class="text-gray-400 font-normal">(PPPoE / WAN)</span></label>
                <input type="text" name="internet_username" value="{{ old('internet_username',$device->internet_username) }}" placeholder="e.g. client@yourisp.com" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Internet Password</label>
                <input type="text" name="internet_password" value="{{ old('internet_password',$device->internet_password) }}" placeholder="PPPoE or WAN password" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
        </div>
        <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-700">
            <i class="fas fa-info-circle mr-1"></i>After saving, use the <strong>"Push Internet Settings"</strong> button on the device page to apply these credentials to the ONU via TR-069.
        </div>
    </div>

    <div class="flex justify-between">
        <a href="{{ route('admin.tr069.show',$device->id) }}" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">← Cancel</a>
        <button type="submit" class="px-6 py-2.5 text-white rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Save Device</button>
    </div>
</form>
</div>
@endsection
