@extends('layouts.admin')
@section('title','Add FAT Node')
@section('page-title','Add FAT Node')
@section('page-subtitle','Register a new Fiber Access Terminal with ONU capacity')
@section('content')
<div class="max-w-2xl">
<form action="{{ route('admin.fat.store') }}" method="POST" class="space-y-4">
    @csrf
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">FAT Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="FAT-Westlands-A" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Code * <span class="font-normal text-gray-400">(alphanumeric, no spaces)</span></label>
                <input type="text" name="code" value="{{ old('code') }}" placeholder="FAT-WL-A1" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Max ONUs (Capacity) *</label>
                <select name="max_onu" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    @foreach([4,8,16,32,64,128] as $c)
                    <option value="{{ $c }}" {{ old('max_onu')==$c?'selected':'' }}>{{ $c }} ports (1:{{ $c }} splitter)</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Splitter Type</label>
                <select name="splitter_type" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    @foreach(['1:4','1:8','1:16','1:32','1:64'] as $s)
                    <option value="{{ $s }}" {{ old('splitter_type')===$s?'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">OLT Port</label>
                <input type="text" name="olt_port" value="{{ old('olt_port') }}" placeholder="PON-1/0/1" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Assigned Router</label>
                <select name="router_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- None --</option>
                    @foreach($routers as $r)<option value="{{ $r->id }}" {{ old('router_id')==$r->id?'selected':'' }}>{{ $r->name }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Assigned Technician</label>
                <select name="technician_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- None --</option>
                    @foreach($technicians as $t)<option value="{{ $t->id }}" {{ old('technician_id')==$t->id?'selected':'' }}>{{ $t->name }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Reseller</label>
                <select name="reseller_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- Main ISP --</option>
                    @foreach($resellers as $r)<option value="{{ $r->id }}" {{ old('reseller_id')==$r->id?'selected':'' }}>{{ $r->company_name }}</option>@endforeach
                </select>
            </div>
        </div>
        <div><label class="block text-xs font-semibold text-gray-600 mb-1">Location / Address</label>
            <input type="text" name="location" value="{{ old('location') }}" placeholder="e.g. Westlands, near KCB Bank" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Latitude</label><input type="number" step="any" name="latitude" value="{{ old('latitude') }}" placeholder="-1.286389" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Longitude</label><input type="number" step="any" name="longitude" value="{{ old('longitude') }}" placeholder="36.817223" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none"></div>
        </div>
        <div><label class="block text-xs font-semibold text-gray-600 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
        </div>
    </div>
    <div class="flex justify-between">
        <a href="{{ route('admin.fat.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">← Back</a>
        <button type="submit" class="px-6 py-2.5 text-white rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Save FAT Node</button>
    </div>
</form>
</div>
@endsection
