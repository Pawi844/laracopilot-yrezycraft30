@extends('layouts.admin')
@section('title','Device Details')
@section('page-title','TR-069 Device Details')
@section('content')
<div class="max-w-2xl">
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <p class="text-gray-400 text-xs mb-1">Serial Number</p>
            <h2 class="text-xl font-black text-gray-800 font-mono">{{ $device->serial_number }}</h2>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $device->status === 'online' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($device->status) }}</span>
    </div>
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-gray-50 rounded-lg p-3"><p class="text-gray-400 text-xs mb-1">Manufacturer</p><p class="text-gray-800 font-semibold">{{ $device->manufacturer ?? 'N/A' }}</p></div>
        <div class="bg-gray-50 rounded-lg p-3"><p class="text-gray-400 text-xs mb-1">Model</p><p class="text-gray-800 font-semibold">{{ $device->model ?? 'N/A' }}</p></div>
        <div class="bg-gray-50 rounded-lg p-3"><p class="text-gray-400 text-xs mb-1">Firmware</p><p class="text-gray-800 font-semibold">{{ $device->firmware_version ?? 'N/A' }}</p></div>
        <div class="bg-gray-50 rounded-lg p-3"><p class="text-gray-400 text-xs mb-1">IP Address</p><p class="text-blue-600 font-mono font-semibold">{{ $device->ip_address ?? 'N/A' }}</p></div>
        <div class="bg-gray-50 rounded-lg p-3"><p class="text-gray-400 text-xs mb-1">MAC Address</p><p class="text-gray-800 font-mono">{{ $device->mac_address ?? 'N/A' }}</p></div>
        <div class="bg-gray-50 rounded-lg p-3"><p class="text-gray-400 text-xs mb-1">Last Inform</p><p class="text-gray-800">{{ $device->last_inform ? $device->last_inform->diffForHumans() : 'Never' }}</p></div>
        <div class="bg-gray-50 rounded-lg p-3 col-span-2"><p class="text-gray-400 text-xs mb-1">Assigned Client</p><p class="text-gray-800 font-semibold">{{ $device->client->username ?? 'Unassigned' }}{{ $device->client ? ' — '.$device->client->full_name : '' }}</p></div>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('admin.tr069.edit', $device->id) }}" class="px-4 py-2.5 text-white rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)">Edit Device</a>
        <form action="{{ route('admin.tr069.reboot', $device->id) }}" method="POST">
            @csrf <button class="px-4 py-2.5 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700"><i class="fas fa-power-off mr-1"></i>Reboot</button>
        </form>
        <a href="{{ route('admin.tr069.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">← Back</a>
    </div>
</div>
</div>
@endsection
