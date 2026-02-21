@extends('layouts.admin')
@section('title','Register TR-069 Device')
@section('page-title','Register TR-069 Device')
@section('content')
<div class="max-w-2xl">
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
    <form action="{{ route('admin.tr069.store') }}" method="POST" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Serial Number *</label><input type="text" name="serial_number" value="{{ old('serial_number') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" required></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Manufacturer</label><input type="text" name="manufacturer" value="{{ old('manufacturer') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="e.g. ZTE, Huawei"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Model</label><input type="text" name="model" value="{{ old('model') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Firmware Version</label><input type="text" name="firmware_version" value="{{ old('firmware_version') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">IP Address</label><input type="text" name="ip_address" value="{{ old('ip_address') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">MAC Address</label><input type="text" name="mac_address" value="{{ old('mac_address') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Assign to Client</label>
                <select name="client_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- Select Client --</option>
                    @foreach($clients as $c)<option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->username }} — {{ $c->full_name }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Reseller</label>
                <select name="reseller_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- MtaaKonnect (Main) --</option>
                    @foreach($resellers as $r)<option value="{{ $r->id }}">{{ $r->company_name }}</option>@endforeach
                </select>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('admin.tr069.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2.5 text-white rounded-lg text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Register Device</button>
        </div>
    </form>
</div>
</div>
@endsection
