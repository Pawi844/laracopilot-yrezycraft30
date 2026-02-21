@extends('layouts.admin')
@section('title','Add NAS')
@section('page-title','Add NAS Server')
@section('content')
<div class="max-w-2xl">
<div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
    <form action="{{ route('admin.nas.store') }}" method="POST" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-slate-300 mb-1">NAS Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" placeholder="e.g. NAS-Nairobi-01" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Short Name *</label>
                <input type="text" name="shortname" value="{{ old('shortname') }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" placeholder="nas-nbi-01" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Type *</label>
                <select name="type" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
                    @foreach(['other','cisco','mikrotik','juniper','zyxel','huawei','ubiquiti'] as $t)
                    <option value="{{ $t }}" {{ old('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-slate-300 mb-1">IP Addresses * <span class="text-slate-500">(comma-separated for multiple)</span></label>
                <input type="text" name="ip_addresses" value="{{ old('ip_addresses') }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono" placeholder="192.168.1.1, 10.0.0.1, 172.16.0.1" required>
                <p class="text-slate-500 text-xs mt-1">Add multiple IPs separated by commas for NAS with multiple interfaces.</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">RADIUS Secret *</label>
                <input type="text" name="secret" value="{{ old('secret') }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono" placeholder="secret123" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Community</label>
                <input type="text" name="community" value="{{ old('community','public') }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" placeholder="public">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Ports</label>
                <input type="number" name="ports" value="{{ old('ports',0) }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Server (Optional)</label>
                <input type="text" name="server" value="{{ old('server') }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Status</label>
                <select name="status" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Reseller (Optional)</label>
                <select name="reseller_id" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">-- MtaaKonnect (Main) --</option>
                    @foreach($resellers as $r)
                    <option value="{{ $r->id }}">{{ $r->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">{{ old('description') }}</textarea>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('admin.nas.index') }}" class="px-4 py-2 border border-slate-600 text-slate-300 rounded-lg text-sm hover:bg-slate-700">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-sky-600 text-white rounded-lg text-sm font-semibold hover:bg-sky-700"><i class="fas fa-save mr-1"></i>Create NAS</button>
        </div>
    </form>
</div>
</div>
@endsection
