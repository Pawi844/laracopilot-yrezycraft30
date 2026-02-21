@extends('layouts.admin')
@section('title','Add OLT')
@section('page-title','Add OLT Device')
@section('page-subtitle','Register an Optical Line Terminal')
@section('content')
<div class="max-w-2xl">
<form action="{{ route('admin.olt.store') }}" method="POST" class="space-y-4">
    @csrf
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">OLT Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. OLT-Westlands-01" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Brand *</label>
                <select name="brand" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                    @foreach(['Huawei','ZTE','Calix','Nokia','Other'] as $b)
                    <option value="{{ $b }}" {{ old('brand','Huawei')===$b?'selected':'' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Model</label>
                <input type="text" name="model" value="{{ old('model') }}" placeholder="e.g. MA5608T, C300" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">IP Address *</label>
                <input type="text" name="ip_address" value="{{ old('ip_address') }}" placeholder="192.168.1.1" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Total GPON Ports *</label>
                <input type="number" name="total_ports" value="{{ old('total_ports',16) }}" min="1" max="256" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none" required>
                <p class="text-gray-400 text-xs mt-1">Port records will be created automatically</p>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">SSH Username</label>
                <input type="text" name="ssh_username" value="{{ old('ssh_username') }}" placeholder="admin" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">SSH Password</label>
                <input type="password" name="ssh_password" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">SNMP Community</label>
                <input type="text" name="snmp_community" value="{{ old('snmp_community','public') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Router</label>
                <select name="router_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    <option value="">-- None --</option>
                    @foreach($routers as $r)<option value="{{ $r->id }}" {{ old('router_id')==$r->id?'selected':'' }}>{{ $r->name }}</option>@endforeach
                </select>
            </div>
            <div class="sm:col-span-2"><label class="block text-xs font-semibold text-gray-600 mb-1">Location</label>
                <input type="text" name="location" value="{{ old('location') }}" placeholder="e.g. Exchange Building, Westlands" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
            </div>
        </div>
    </div>
    <div class="flex justify-between">
        <a href="{{ route('admin.olt.index') }}" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600">← Back</a>
        <button type="submit" class="px-6 py-2.5 text-white rounded-xl text-sm font-semibold" style="background:linear-gradient(90deg,#f97316,#ea580c)"><i class="fas fa-save mr-1"></i>Add OLT</button>
    </div>
</form>
</div>
@endsection
