@extends('layouts.admin')
@section('title','Create PPPoE Plan')
@section('page-title','Create PPPoE Plan')
@section('content')
<div class="max-w-2xl">
<div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
    <form action="{{ route('admin.plans.store') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="type" value="pppoe">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-slate-300 mb-1">Plan Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" placeholder="e.g. Home Fiber 10Mbps" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Price (KES) *</label>
                <input type="number" name="price" value="{{ old('price') }}" step="0.01" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Billing Cycle *</label>
                <select name="billing_cycle" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none" required>
                    @foreach(['monthly','quarterly','yearly','weekly','daily'] as $c)
                    <option value="{{ $c }}">{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Download Speed</label>
                <input type="text" name="speed_download" value="{{ old('speed_download') }}" placeholder="10M or 10240k" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Upload Speed</label>
                <input type="text" name="speed_upload" value="{{ old('speed_upload') }}" placeholder="5M or 5120k" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Data Limit</label>
                <input type="text" name="data_limit" value="{{ old('data_limit') }}" placeholder="Unlimited or 50GB" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">MikroTik Profile Name</label>
                <input type="text" name="profile_name" value="{{ old('profile_name') }}" placeholder="pppoe-home-10m" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Address Pool</label>
                <input type="text" name="address_pool" value="{{ old('address_pool') }}" placeholder="pppoe-pool" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Burst Limit</label>
                <input type="text" name="burst_limit" value="{{ old('burst_limit') }}" placeholder="20M/10M" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Session Timeout (sec)</label>
                <input type="number" name="session_timeout" value="{{ old('session_timeout') }}" placeholder="0 = unlimited" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Idle Timeout (sec)</label>
                <input type="number" name="idle_timeout" value="{{ old('idle_timeout') }}" placeholder="300" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Reseller</label>
                <select name="reseller_id" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">-- MtaaKonnect (Main) --</option>
                    @foreach($resellers as $r)
                    <option value="{{ $r->id }}">{{ $r->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="active" value="1" checked class="rounded">
                    <span class="text-slate-300 text-sm">Active Plan</span>
                </label>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">{{ old('description') }}</textarea>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-2">
            <a href="{{ route('admin.plans.index') }}" class="px-4 py-2 border border-slate-600 text-slate-300 rounded-lg text-sm hover:bg-slate-700">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-sky-600 text-white rounded-lg text-sm font-semibold hover:bg-sky-700"><i class="fas fa-save mr-1"></i>Create Plan</button>
        </div>
    </form>
</div>
</div>
@endsection
