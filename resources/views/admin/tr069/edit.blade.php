@extends('layouts.admin')
@section('title','Edit TR-069 Device')
@section('page-title','Edit TR-069 Device')
@section('content')
<div class="max-w-2xl">
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
    <form action="{{ route('admin.tr069.update', $device->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Serial Number</label><input type="text" value="{{ $device->serial_number }}" class="w-full border border-gray-100 bg-gray-50 rounded-lg px-3 py-2.5 text-sm font-mono text-gray-500" disabled></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Manufacturer</label><input type="text" name="manufacturer" value="{{ old('manufacturer',$device->manufacturer) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Model</label><input type="text" name="model" value="{{ old('model',$device->model) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Firmware</label><input type="text" name="firmware_version" value="{{ old('firmware_version',$device->firmware_version) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">IP Address</label><input type="text" name="ip_address" value="{{ old('ip_address',$device->ip_address) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">MAC Address</label><input type="text" name="mac_address" value="{{ old('mac_address',$device->mac_address) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    @foreach(['online','offline','unknown','error'] as $s)
                    <option value="{{ $s }}" {{ old('status',$device->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Assign to Client</label>
                <select name="client_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- Select Client --</option>
                    @foreach($clients as $c)<option value="{{ $c->id }}" {{ old('client_id',$device->client_id) == $c->id ? 'selected' : '' }}>{{ $c->username }}</option>@endforeach
                </select>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('admin.tr069.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2.5 text-white rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Update Device</button>
        </div>
    </form>
</div>
</div>
@endsection
