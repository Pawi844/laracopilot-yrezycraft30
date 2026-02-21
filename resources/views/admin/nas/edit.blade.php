@extends('layouts.admin')
@section('title','Edit NAS')
@section('page-title','Edit NAS Server')
@section('content')
<div class="max-w-2xl">
<div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
    <form action="{{ route('admin.nas.update', $nas->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-slate-300 mb-1">NAS Name</label>
                <input type="text" name="name" value="{{ old('name',$nas->name) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Short Name</label>
                <input type="text" name="shortname" value="{{ old('shortname',$nas->shortname) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Type</label>
                <select name="type" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    @foreach(['other','cisco','mikrotik','juniper','zyxel','huawei','ubiquiti'] as $t)
                    <option value="{{ $t }}" {{ old('type',$nas->type) === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-slate-300 mb-1">IP Addresses (comma-separated)</label>
                <input type="text" name="ip_addresses" value="{{ old('ip_addresses', implode(', ', $nas->ip_addresses ?? [])) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">RADIUS Secret</label>
                <input type="text" name="secret" value="{{ old('secret',$nas->secret) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Status</label>
                <select name="status" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    @foreach(['active','inactive','unreachable'] as $s)
                    <option value="{{ $s }}" {{ old('status',$nas->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Community</label>
                <input type="text" name="community" value="{{ old('community',$nas->community) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Reseller</label>
                <select name="reseller_id" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">-- MtaaKonnect (Main) --</option>
                    @foreach($resellers as $r)
                    <option value="{{ $r->id }}" {{ old('reseller_id',$nas->reseller_id) == $r->id ? 'selected' : '' }}>{{ $r->company_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('admin.nas.index') }}" class="px-4 py-2 border border-slate-600 text-slate-300 rounded-lg text-sm hover:bg-slate-700">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-sky-600 text-white rounded-lg text-sm font-semibold hover:bg-sky-700"><i class="fas fa-save mr-1"></i>Update NAS</button>
        </div>
    </form>
</div>
</div>
@endsection
